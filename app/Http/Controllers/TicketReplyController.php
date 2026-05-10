<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class TicketReplyController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $user = auth()->user();

        // Prevent requesters from replying to other people's tickets
        if ($user->isRequester()) {
            $requester = $user->requester;
            if (!$requester || $ticket->requester_id !== $requester->id) {
                abort(403, 'You are not authorized to reply to this ticket.');
            }
        }

        // ✅ Block agents from replying to tickets not assigned to them
        if ($user->isAgent() && $ticket->assigned_user_id !== $user->id) {
            abort(403, 'You are not authorized to reply to this ticket.');
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $ticket->replies()->create([
            'user_id'     => $user->id,
            'message'     => $validated['message'],
            'is_internal' => $user->isRequester() ? false : $request->has('is_internal'),
        ]);

        $ticket->touch();

        // ✅ Log reply
        ActivityLog::log(
            'reply_added',
            "Added a reply to ticket #{$ticket->id}: \"{$ticket->subject}\"",
            'Ticket',
            $ticket->id
        );

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Reply added successfully.');
    }

    public function destroy(Ticket $ticket, TicketReply $reply)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && !$user->isSupervisor()) {
            abort(403, 'You are not authorized to delete replies.');
        }

        if ($reply->ticket_id !== $ticket->id) {
            abort(404);
        }

        // ✅ Log before delete
        ActivityLog::log(
            'reply_deleted',
            "Deleted a reply from ticket #{$ticket->id}: \"{$ticket->subject}\"",
            'Ticket',
            $ticket->id
        );

        $reply->delete();

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Reply deleted successfully.');
    }
}