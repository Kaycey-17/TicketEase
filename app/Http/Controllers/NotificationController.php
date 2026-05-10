<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Return unassigned open tickets as JSON for the bell dropdown.
     * Also marks the notifications as "seen" in the session.
     */
    public function index()
    {
        $user    = auth()->user();
        $tickets = $this->getUnassignedTickets($user);

        // Mark as seen — clears the red badge until a new ticket comes in
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

    /**
     * Return just the unread count for the badge.
     * "Unread" = tickets created after the last time the bell was opened.
     */
    public function count()
    {
        $user    = auth()->user();
        $tickets = $this->getUnassignedTickets($user);

        $seenAt = session('notifications_seen_at');

        // If never seen, all unassigned tickets count as unread
        if (!$seenAt) {
            $unreadCount = $tickets->count();
        } else {
            $unreadCount = $tickets->filter(
                fn($t) => $t->created_at->toDateTimeString() > $seenAt
            )->count();
        }

        return response()->json(['count' => $unreadCount]);
    }

    /**
     * Get unassigned open tickets scoped by role.
     */
    private function getUnassignedTickets($user)
    {
        // Agents only see their assigned tickets — not unassigned ones
        // So for agents we show tickets assigned to them that are still open
        if ($user->isAgent()) {
            return Ticket::with(['requester', 'category'])
                ->where('assigned_user_id', $user->id)
                ->where('status', 'open')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        // Admins and supervisors see all unassigned open tickets
        return Ticket::with(['requester', 'category'])
            ->whereNull('assigned_user_id')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
}