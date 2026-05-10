<?php

namespace App\Http\Controllers;

use App\Models\Requester;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class RequesterController extends Controller
{
    public function index(Request $request)
    {
        $query = Requester::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $requesters = $query->withCount('tickets')
                            ->orderBy('first_name')
                            ->paginate(20);

        return view('requesters.index', compact('requesters'));
    }

    public function create()
    {
        return view('requesters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:requesters,email',
            'phone'      => 'nullable|string|max:255',
            'company'    => 'nullable|string|max:255',
        ]);

        $requester = Requester::create($validated);

        ActivityLog::log(
            'requester_created',
            "Created new requester \"{$requester->full_name}\"",
            'Requester',
            $requester->id
        );

        return redirect()->route('requesters.index')
            ->with('success', 'Requester created successfully.');
    }

    public function show(Requester $requester)
    {
        $stats = [
            'total'       => $requester->tickets()->count(),
            'open'        => $requester->tickets()->where('status', 'open')->count(),
            'in_progress' => $requester->tickets()->where('status', 'in_progress')->count(),
            'pending'     => $requester->tickets()->where('status', 'pending')->count(),
            'resolved'    => $requester->tickets()->whereIn('status', ['resolved', 'closed'])->count(),
        ];

        $tickets = $requester->tickets()
            ->with(['category', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('requesters.show', compact('requester', 'stats', 'tickets'));
    }

    public function edit(Requester $requester)
    {
        return view('requesters.edit', compact('requester'));
    }

    public function update(Request $request, Requester $requester)
    {
        $user = auth()->user();

        // ── Agent: can only update phone and company ──────────────────
        if ($user->isAgent()) {
            $validated = $request->validate([
                'phone'   => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
            ]);

            $requester->update($validated);

            ActivityLog::log(
                'requester_updated',
                "Updated requester \"{$requester->full_name}\" (contact info only)",
                'Requester',
                $requester->id
            );

            return redirect()->route('requesters.show', $requester)
                ->with('success', 'Requester contact info updated successfully.');
        }

        // ── Admin / Supervisor: full update ───────────────────────────
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:requesters,email,' . $requester->id,
            'phone'      => 'nullable|string|max:255',
            'company'    => 'nullable|string|max:255',
        ]);

        $requester->update($validated);

        ActivityLog::log(
            'requester_updated',
            "Updated requester \"{$requester->full_name}\"",
            'Requester',
            $requester->id
        );

        return redirect()->route('requesters.show', $requester)
            ->with('success', 'Requester updated successfully.');
    }

    public function destroy(Requester $requester)
    {
        // Agents cannot delete requesters
        if (!auth()->user()->isAdmin() && !auth()->user()->isSupervisor()) {
            abort(403, 'You are not authorized to delete requesters.');
        }

        ActivityLog::log(
            'requester_deleted',
            "Deleted requester \"{$requester->full_name}\"",
            'Requester',
            $requester->id
        );

        $requester->delete();

        return redirect()->route('requesters.index')
            ->with('success', 'Requester deleted successfully.');
    }
}