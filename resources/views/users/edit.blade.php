<x-app-layout>
    <x-slot name="header">Edit User</x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Back button --}}
            <div class="mb-5">
                <a href="{{ route('users.show', $user) }}"
                    class="inline-flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-100 transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to {{ $user->name }}
                </a>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Edit User</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Update account details for {{ $user->name }}.</p>
                    </div>
                    @php
                        $roleColors = [
                            'admin'         => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
                            'supervisor'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                            'support_agent' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                            'requester'     => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
                        ];
                        $roleLabels = [
                            'admin'         => 'Administrator',
                            'supervisor'    => 'Supervisor',
                            'support_agent' => 'Support Agent',
                            'requester'     => 'Requester',
                        ];
                    @endphp
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $roleColors[$user->role] ?? '' }}">
                        {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                    </span>
                </div>

                <form method="POST" action="{{ route('users.update', $user) }}" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select name="role" required
                            class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('role') border-red-400 @enderror">
                            @foreach($roles as $value => $label)
                                <option value="{{ $value }}" {{ old('role', $user->role) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role info --}}
                    <div class="p-4 bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900 rounded-lg">
                        <p class="text-xs font-semibold text-blue-800 dark:text-blue-300 mb-2">Role Descriptions</p>
                        <ul class="text-xs text-blue-700 dark:text-blue-400 space-y-1">
                            <li><strong>Administrator:</strong> Full system access including user management</li>
                            <li><strong>Supervisor:</strong> Manage tickets, view reports, and oversee agents</li>
                            <li><strong>Support Agent:</strong> Handle and respond to assigned tickets</li>
                            <li><strong>Requester:</strong> Submit and track their own tickets</li>
                        </ul>
                    </div>

                    {{-- Change Password --}}
                    <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-100 dark:border-amber-900 rounded-lg space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-amber-800 dark:text-amber-300">Change Password</p>
                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">Leave blank to keep the current password.</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">New Password</label>
                                <input type="password" name="password"
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('password') border-red-400 @enderror">
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-slate-400">Minimum 8 characters</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Confirm New Password</label>
                                <input type="password" name="password_confirmation"
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="submit"
                            class="px-5 py-2.5 btn-accent text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                            Update User
                        </button>
                        <a href="{{ route('users.show', $user) }}"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg transition-colors">
                            Cancel
                        </a>
                    </div>

                    <p class="text-xs text-slate-400 dark:text-slate-600 pt-1">
                        User ID: {{ $user->id }} &nbsp;·&nbsp;
                        Created: {{ $user->created_at->format('M d, Y') }} &nbsp;·&nbsp;
                        Last Updated: {{ $user->updated_at->format('M d, Y') }}
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>