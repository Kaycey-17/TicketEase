<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Requester;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);

        $roleCounts = [
            'admin'         => User::where('role', 'admin')->count(),
            'supervisor'    => User::where('role', 'supervisor')->count(),
            'support_agent' => User::where('role', 'support_agent')->count(),
            'requester'     => User::where('role', 'requester')->count(),
        ];

        return view('users.index', compact('users', 'roleCounts'));
    }

    public function create()
    {
        $roles = [
            'admin'         => 'Administrator',
            'supervisor'    => 'Supervisor',
            'support_agent' => 'Support Agent',
            'requester'     => 'Requester',
        ];

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in(['admin', 'supervisor', 'support_agent', 'requester'])],
        ];

        if ($request->role === 'requester') {
            $rules['first_name'] = ['required', 'string', 'max:255'];
            $rules['last_name']  = ['required', 'string', 'max:255'];
            $rules['phone']      = ['nullable', 'string', 'max:20'];
            $rules['company']    = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        if ($validated['role'] === 'requester') {
            Requester::create([
                'user_id'    => $user->id,
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
                'email'      => $validated['email'],
                'phone'      => $validated['phone'] ?? null,
                'company'    => $validated['company'] ?? null,
            ]);
        }

        ActivityLog::log(
            'user_created',
            "Created new user \"{$user->name}\" with role \"{$user->role}\"",
            'User',
            $user->id
        );

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = [
            'admin'         => 'Administrator',
            'supervisor'    => 'Supervisor',
            'support_agent' => 'Support Agent',
            'requester'     => 'Requester',
        ];

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'     => ['required', Rule::in(['admin', 'supervisor', 'support_agent', 'requester'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->role  = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        ActivityLog::log(
            'user_updated',
            "Updated user \"{$user->name}\"",
            'User',
            $user->id
        );

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $assignedTicketsCount = $user->assignedTickets()->count();

        if ($assignedTicketsCount > 0) {
            return redirect()->route('users.index')
                ->with('error', "Cannot delete user. They have {$assignedTicketsCount} assigned ticket(s).");
        }

        ActivityLog::log(
            'user_deleted',
            "Deleted user \"{$user->name}\" with role \"{$user->role}\"",
            'User',
            $user->id
        );

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
