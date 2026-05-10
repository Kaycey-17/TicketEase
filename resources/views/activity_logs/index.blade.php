<x-app-layout>
    <x-slot name="header">
        Activity Logs
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Activity Logs</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Full history of system activities
                    </p>
                </div>

                {{-- Clear logs — admin only --}}
                @if(auth()->user()->isAdmin())
                    <form action="{{ route('activity_logs.clear') }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to clear all activity logs? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm">
                            Clear All Logs
                        </button>
                    </form>
                @endif
            </div>

            {{-- Filters --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('activity_logs.index') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Action Type
                        </label>
                        <select name="action"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Actions</option>
                            <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Created</option>
                            <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Updated</option>
                            <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                            <option value="reply"   {{ request('action') === 'reply' ? 'selected' : '' }}>Replies</option>
                            <option value="login"   {{ request('action') === 'login' ? 'selected' : '' }}>Login</option>
                            <option value="logout"  {{ request('action') === 'logout' ? 'selected' : '' }}>Logout</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                        <select name="user_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date From
                        </label>
                        <input type="date" name="date_from"
                            value="{{ request('date_from') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date To
                        </label>
                        <input type="date" name="date_to"
                            value="{{ request('date_to') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-4 flex gap-2">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                            Apply Filters
                        </button>
                        <a href="{{ route('activity_logs.index') }}"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md text-sm">
                            Clear Filters
                        </a>
                    </div>
                </form>
            </div>

            {{-- Logs Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <p>{{ $log->created_at->format('M d, Y') }}</p>
                                        <p class="text-xs text-gray-400">
                                            {{ $log->created_at->format('h:i A') }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ $log->created_at->diffForHumans() }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($log->user)
                                            <div class="flex items-center gap-2">
                                                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold">
                                                    {{ substr($log->user->name, 0, 2) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $log->user->name }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ ucfirst(str_replace('_', ' ', $log->user->role)) }}
                                                    </p>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">System</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $log->action_color }}">
                                            {{ $log->action_icon }}
                                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $log->description }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->ip_address ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        No activity logs found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $logs->appends(request()->all())->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>