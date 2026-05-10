<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Requester;
use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['requester', 'category', 'assignedUser']);
        $user  = auth()->user();

        if ($user->isRequester()) {
            $requester = $user->requester;
            if ($requester) {
                $query->where('requester_id', $requester->id);
            }
        } elseif ($user->isAgent()) {
            $query->where('assigned_user_id', $user->id);
        }

        if ($request->filled('status'))      { $query->where('status', $request->status); }
        if ($request->filled('priority'))     { $query->where('priority', $request->priority); }
        if ($request->filled('category_id')) { $query->where('category_id', $request->category_id); }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $tickets    = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = Category::where('is_active', true)->get();

        return view('tickets.index', compact('tickets', 'categories'));
    }

    public function create()
    {
        $user       = auth()->user();
        $categories = Category::where('is_active', true)->get();
        $agents     = User::whereIn('role', ['admin', 'supervisor', 'support_agent'])->orderBy('name')->get();
        $requesters = $user->isRequester() ? collect() : Requester::orderBy('first_name')->get();

        return view('tickets.create', compact('requesters', 'categories', 'agents'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'category_id'      => 'required|exists:categories,id',
            'subject'          => 'required|string|max:255',
            'description'      => 'required|string',
            'priority'         => 'required|in:low,medium,high,critical',
            'assigned_user_id' => 'nullable|exists:users,id',
        ];

        if (!$user->isRequester()) {
            $rules['requester_id'] = 'required|exists:requesters,id';
        }

        $validated = $request->validate($rules);

        if ($user->isRequester()) {
            $requester = $user->requester;
            if (!$requester) {
                return back()->with('error', 'No requester profile found. Please contact an administrator.');
            }
            $validated['requester_id']     = $requester->id;
            $validated['assigned_user_id'] = null;
        }

        $validated['created_by'] = $user->id;
        $validated['status']     = 'open';

        $ticket = Ticket::create($validated);

        // ✅ Record initial status history when ticket is created
        TicketStatusHistory::create([
            'ticket_id'   => $ticket->id,
            'changed_by'  => $user->id,
            'from_status' => null,
            'to_status'   => 'open',
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket)
    {
        $user = auth()->user();

        if ($user->isRequester()) {
            $requester = $user->requester;
            if (!$requester || $ticket->requester_id !== $requester->id) {
                abort(403, 'You are not authorized to view this ticket.');
            }
        }

        if ($user->isAgent() && $ticket->assigned_user_id !== $user->id) {
            abort(403, 'You are not authorized to view this ticket.');
        }

        // ✅ Load status histories alongside other relationships
        $ticket->load([
            'requester',
            'category',
            'assignedUser',
            'creator',
            'replies.user',
            'statusHistories.changedBy',
        ]);

        $agents = User::whereIn('role', ['admin', 'supervisor', 'support_agent'])
                      ->orderBy('name')
                      ->get();

        return view('tickets.show', compact('ticket', 'agents'));
    }

    public function edit(Ticket $ticket)
    {
        $user = auth()->user();

        if ($user->isRequester()) {
            $requester = $user->requester;
            if (!$requester || $ticket->requester_id !== $requester->id) {
                abort(403, 'You are not authorized to edit this ticket.');
            }
            if (in_array($ticket->status, ['resolved', 'closed'])) {
                return redirect()->route('tickets.show', $ticket)
                    ->with('error', 'You cannot edit a resolved or closed ticket.');
            }
        }

        if ($user->isAgent() && $ticket->assigned_user_id !== $user->id) {
            abort(403, 'You are not authorized to edit this ticket.');
        }

        if ($ticket->status === 'closed' && !$user->isRequester()) {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'This ticket is closed and cannot be edited.');
        }

        $requesters = $user->isRequester() ? collect() : Requester::orderBy('first_name')->get();
        $categories = Category::where('is_active', true)->get();
        $agents     = User::whereIn('role', ['admin', 'supervisor', 'support_agent'])->orderBy('name')->get();

        return view('tickets.edit', compact('ticket', 'requesters', 'categories', 'agents'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $user = auth()->user();

        // ── Requester: subject + description only ─────────────────────
        if ($user->isRequester()) {
            $requester = $user->requester;
            if (!$requester || $ticket->requester_id !== $requester->id) {
                abort(403, 'You are not authorized to update this ticket.');
            }

            $validated = $request->validate([
                'subject'     => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $ticket->update($validated);

            return redirect()->route('tickets.show', $ticket)
                ->with('success', 'Ticket updated successfully.');
        }

        // ── Agent: status + assigned_user_id only ─────────────────────
        if ($user->isAgent()) {
            if ($ticket->assigned_user_id !== $user->id) {
                abort(403, 'You are not authorized to update this ticket.');
            }

            if ($ticket->status === 'closed') {
                return redirect()->route('tickets.show', $ticket)
                    ->with('error', 'This ticket is closed and cannot be updated.');
            }

            $validated = $request->validate([
                'status'           => 'required|in:open,in_progress,pending,resolved,closed',
                'assigned_user_id' => 'nullable|exists:users,id',
            ]);

            if ($validated['status'] !== $ticket->status) {
                if (!$ticket->canTransitionTo($validated['status'])) {
                    return back()->with('error',
                        "Cannot change status from \"" . ucfirst(str_replace('_', ' ', $ticket->status)) .
                        "\" to \"" . ucfirst(str_replace('_', ' ', $validated['status'])) . "\". " .
                        "Allowed: " . implode(', ', array_map(
                            fn($s) => ucfirst(str_replace('_', ' ', $s)),
                            $ticket->getAllowedStatusTransitions()
                        ))
                    );
                }
            }

            // ✅ Capture old status before update
            $oldStatus = $ticket->status;

            $ticket->update($validated);

            // ✅ Record status history if status changed
            if ($oldStatus !== $validated['status']) {
                TicketStatusHistory::create([
                    'ticket_id'   => $ticket->id,
                    'changed_by'  => $user->id,
                    'from_status' => $oldStatus,
                    'to_status'   => $validated['status'],
                ]);
            }

            $fresh = $ticket->fresh();
            if (in_array($fresh->status, ['resolved', 'closed']) && !$ticket->resolved_at) {
                $ticket->update(['resolved_at' => now()]);
            }
            if (!in_array($fresh->status, ['resolved', 'closed']) && $ticket->resolved_at) {
                $ticket->update(['resolved_at' => null]);
            }

            return redirect()->route('tickets.show', $ticket)
                ->with('success', 'Ticket updated successfully.');
        }

        // ── Admin / Supervisor: full edit ─────────────────────────────
        if ($ticket->status === 'closed') {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'This ticket is closed and cannot be updated.');
        }

        $validated = $request->validate([
            'requester_id'     => 'required|exists:requesters,id',
            'category_id'      => 'required|exists:categories,id',
            'subject'          => 'required|string|max:255',
            'description'      => 'required|string',
            'status'           => 'required|in:open,in_progress,pending,resolved,closed',
            'priority'         => 'required|in:low,medium,high,critical',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        if ($validated['status'] !== $ticket->status) {
            if (!$ticket->canTransitionTo($validated['status'])) {
                return back()->with('error',
                    "Cannot change status from \"" . ucfirst(str_replace('_', ' ', $ticket->status)) .
                    "\" to \"" . ucfirst(str_replace('_', ' ', $validated['status'])) . "\". " .
                    "Allowed: " . implode(', ', array_map(
                        fn($s) => ucfirst(str_replace('_', ' ', $s)),
                        $ticket->getAllowedStatusTransitions()
                    ))
                );
            }
        }

        // ✅ Capture old status before update
        $oldStatus = $ticket->status;

        $ticket->update($validated);

        // ✅ Record status history if status changed
        if ($oldStatus !== $validated['status']) {
            TicketStatusHistory::create([
                'ticket_id'   => $ticket->id,
                'changed_by'  => $user->id,
                'from_status' => $oldStatus,
                'to_status'   => $validated['status'],
            ]);
        }

        $fresh = $ticket->fresh();
        if (in_array($fresh->status, ['resolved', 'closed']) && !$ticket->resolved_at) {
            $ticket->update(['resolved_at' => now()]);
        }
        if (!in_array($fresh->status, ['resolved', 'closed']) && $ticket->resolved_at) {
            $ticket->update(['resolved_at' => null]);
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && !$user->isSupervisor()) {
            abort(403, 'You are not authorized to delete tickets.');
        }

        ActivityLog::log(
            'ticket_deleted',
            "Deleted ticket #{$ticket->id}: \"{$ticket->subject}\"",
            'Ticket',
            $ticket->id
        );

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }
}