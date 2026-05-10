<x-app-layout>
    <x-slot name="header">Create New User</x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Back button --}}
            <div class="mb-5">
                <a href="{{ route('users.index') }}"
                    class="inline-flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-100 transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Users
                </a>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">User Information</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Create a new system user and assign their role.</p>
                </div>

                <form method="POST" action="{{ route('users.store') }}" class="p-6 space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select name="role" id="role-select" required
                                onchange="toggleRequesterFields(this.value)"
                                class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('role') border-red-400 @enderror">
                                <option value="">-- Select Role --</option>
                                @foreach($roles as $value => $label)
                                    <option value="{{ $value }}" {{ old('role') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Role info box --}}
                    <div class="p-4 bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900 rounded-lg">
                        <p class="text-xs font-semibold text-blue-800 dark:text-blue-300 mb-2">Role Descriptions</p>
                        <ul class="text-xs text-blue-700 dark:text-blue-400 space-y-1">
                            <li><strong>Administrator:</strong> Full system access including user management</li>
                            <li><strong>Supervisor:</strong> Manage tickets, view reports, and oversee agents</li>
                            <li><strong>Support Agent:</strong> Handle and respond to assigned tickets</li>
                            <li><strong>Requester:</strong> Submit and track their own tickets</li>
                        </ul>
                    </div>

                    {{-- Requester profile fields --}}
                    <div id="requester-fields"
                        class="{{ old('role') === 'requester' ? '' : 'hidden' }} p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900 rounded-lg space-y-4">
                        <p class="text-xs font-semibold text-emerald-800 dark:text-emerald-300">Requester Profile Information</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}"
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('first_name') border-red-400 @enderror">
                                @error('first_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}"
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('last_name') border-red-400 @enderror">
                                @error('last_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Company</label>
                                <input type="text" name="company" value="{{ old('company') }}"
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" required
                                class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('password') border-red-400 @enderror">
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-slate-400">Minimum 8 characters</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" required
                                class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="submit"
                            class="px-5 py-2.5 btn-accent text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                            Create User
                        </button>
                        <a href="{{ route('users.index') }}"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg transition-colors">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleRequesterFields(role) {
            const fields = document.getElementById('requester-fields');
            fields.classList.toggle('hidden', role !== 'requester');
        }
        document.addEventListener('DOMContentLoaded', function () {
            toggleRequesterFields(document.getElementById('role-select').value);
        });
    </script>
</x-app-layout>