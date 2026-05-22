<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function index()
    {
        $user    = auth()->user();
        $tickets = $this->getUnassignedTickets($user);


        session(['notifications_seen_at' => now()->toDateTimeString()]);

        return response()->json([
            'tickets' => $tickets->map(fn($t) => [
                'id'         => $t->id,
                'subject'    => $t->subject,
                'priority'   => $t->priority,
                'created_at' => $t->created_at->diffForHumans(),
                'url'        => route('tickets.show', $t),
                'requester'  => $t->requester?->full_name ?? 'Unknown',
                'category'   => $t->category?->name ?? 'Uncategorized',
            ]),
            'count' => $tickets->count(),
        ]);
    }


    public function count()
    {
        $user    = auth()->user();
        $tickets = $this->getUnassignedTickets($user);

        $seenAt = session('notifications_seen_at');


        if (!$seenAt) {
            $unreadCount = $tickets->count();
        } else {
            $unreadCount = $tickets->filter(
                fn($t) => $t->created_at->toDateTimeString() > $seenAt
            )->count();
        }

        return response()->json(['count' => $unreadCount]);
    }


    private function getUnassignedTickets($user)
    {

        if ($user->isAgent()) {
            return Ticket::with(['requester', 'category'])
                ->where('assigned_user_id', $user->id)
                ->where('status', 'open')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }


        return Ticket::with(['requester', 'category'])
            ->whereNull('assigned_user_id')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
}
