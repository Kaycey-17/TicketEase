<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isRequester()) {
            $requester = $user->requester;

            if (!$requester) {
                $stats = [
                    'total_tickets'       => 0,
                    'open_tickets'        => 0,
                    'in_progress_tickets' => 0,
                    'pending_tickets'     => 0,
                    'resolved_tickets'    => 0,
                ];
                $recentTickets = collect();
            } else {
                $baseQuery = Ticket::where('requester_id', $requester->id);

                $stats = [
                    'total_tickets'       => (clone $baseQuery)->count(),
                    'open_tickets'        => (clone $baseQuery)->where('status', 'open')->count(),
                    'in_progress_tickets' => (clone $baseQuery)->where('status', 'in_progress')->count(),
                    'pending_tickets'     => (clone $baseQuery)->where('status', 'pending')->count(),
                    'resolved_tickets'    => (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])->count(),
                ];

                $recentTickets = Ticket::with(['requester', 'category', 'assignedUser'])
                    ->where('requester_id', $requester->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            }

        } elseif ($user->isAgent()) {
            $baseQuery = Ticket::where('assigned_user_id', $user->id);

            $stats = [
                'total_tickets'       => (clone $baseQuery)->count(),
                'open_tickets'        => (clone $baseQuery)->where('status', 'open')->count(),
                'in_progress_tickets' => (clone $baseQuery)->where('status', 'in_progress')->count(),
                'pending_tickets'     => (clone $baseQuery)->where('status', 'pending')->count(),
                'resolved_tickets'    => (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])->count(),
            ];

            $recentTickets = Ticket::with(['requester', 'category', 'assignedUser'])
                ->where('assigned_user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

        } else {
            $stats = [
                'total_tickets'       => Ticket::count(),
                'open_tickets'        => Ticket::where('status', 'open')->count(),
                'in_progress_tickets' => Ticket::where('status', 'in_progress')->count(),
                'pending_tickets'     => Ticket::where('status', 'pending')->count(),
                'resolved_tickets'    => Ticket::whereIn('status', ['resolved', 'closed'])->count(),
            ];

            $recentTickets = Ticket::with(['requester', 'category', 'assignedUser'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        
        $recentActivity = collect();
        if ($user->isAdmin() || $user->isSupervisor()) {
            $recentActivity = ActivityLog::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        return view('dashboard', compact('stats', 'recentTickets', 'recentActivity'));
    }
}
