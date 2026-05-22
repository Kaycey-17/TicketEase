<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        
        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs  = $query->paginate(20);
        $users = \App\Models\User::orderBy('name')->get();

        return view('activity_logs.index', compact('logs', 'users'));
    }

    public function clear()
    {
        
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        ActivityLog::truncate();

        return redirect()->route('activity_logs.index')
            ->with('success', 'Activity logs cleared successfully.');
    }
}
