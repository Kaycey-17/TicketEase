<x-app-layout>
    <x-slot name="header">User Management</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <!-- Role Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $roleStats = [
                        ['label' => 'Administrators', 'key' => 'admin',         'color' => 'purple', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'num' => 'text-purple-900', 'border' => 'border-purple-100'],
                        ['label' => 'Supervisors',    'key' => 'supervisor',     'color' => 'blue',   'bg' => 'bg-blue-50',   'text' => 'text-blue-700',   'num' => 'text-blue-900',   'border' => 'border-blue-100'],
                        ['label' => 'Support Agents', 'key' => 'support_agent', 'color' => 'emerald','bg' => 'bg-emerald-50','text' => 'text-emerald-700','num' => 'text-emerald-900','border' => 'border-emerald-100'],
                        ['label' => 'Requesters',     'key' => 'requester',     'color' => 'slate',  'bg' => 'bg-slate-50',  'text' => 'text-slate-600',  'num' => 'text-slate-800',  'border' => 'border-slate-200'],
                    ];
                @endphp
                @foreach($roleStats as $stat)
                    <div class="bg-white rounded-xl border {{ $stat['border'] }} p-5 shadow-sm">
                        <p class="text-xs font-semibold {{ $stat['text'] }} mb-1">{{ $stat['label'] }}</p>
                        <p class="text-3xl font-bold {{ $stat['num'] }}">{{ $roleCounts[$stat['key']] ?? 0 }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Header + Create -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">All Users</h2>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $users->total() }} users in the system</p>
                </div>
                <a href="{{ route('users.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create User
                </a>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Joined</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($users as $user)
                                @php
                                    $roleColors = [
                                        'admin'         => 'bg-purple-100 text-purple-700',
                                        'supervisor'    => 'bg-blue-100 text-blue-700',
                                        'support_agent' => 'bg-emerald-100 text-emerald-700',
                                        'requester'     => 'bg-slate-100 text-slate-600',
                                    ];
                                    $roleLabels = [
                                        'admin'         => 'Administrator',
                                        'supervisor'    => 'Supervisor',
                                        'support_agent' => 'Support Agent',
                                        'requester'     => 'Requester',
                                    ];
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0 shadow-sm">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-800">
                                                    {{ $user->name }}
                                                    @if($user->id === auth()->id())
                                                        <span class="ml-1 text-xs text-blue-500 font-normal">(You)</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="text-sm text-slate-500">{{ $user->email }}</span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $roleColors[$user->role] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="text-sm text-slate-500">{{ $user->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('users.show', $user) }}"
                                                class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                                View
                                            </a>
                                            <a href="{{ route('users.edit', $user) }}"
                                                class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                                Edit
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                                                    onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-4 border-t border-slate-100 bg-slate-50">
                    {{ $users->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>