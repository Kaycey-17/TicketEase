<x-app-layout>
    <x-slot name="header">User Details</x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- User Info Card -->
                <div class="space-y-5">
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="p-6 flex flex-col items-center text-center border-b border-slate-100 dark:border-slate-800">
                            <div class="h-16 w-16 rounded-full btn-accent flex items-center justify-center text-white text-2xl font-bold mb-3 shadow-sm">
                                {{ substr($user->name, 0, 2) }}
                            </div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $user->name }}</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $user->email }}</p>
                            <span class="mt-2 px-2.5 py-1 text-xs font-semibold rounded-full {{ $roleColors[$user->role] ?? '' }}">
                                {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                            </span>
                        </div>

                        <div class="p-5 space-y-3">
                            <div>
                                <p class="text-xs font-medium text-slate-400 uppercase tracking-wide">Member Since</p>
                                <p class="text-sm text-slate-800 dark:text-slate-200 mt-0.5">{{ $user->created_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-400 uppercase tracking-wide">Last Updated</p>
                                <p class="text-sm text-slate-800 dark:text-slate-200 mt-0.5">{{ $user->updated_at->format('M d, Y') }}</p>
                            </div>

                            @if($user->role === 'requester' && $user->requester)
                                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-2">Requester Profile</p>
                                    <div class="space-y-2">
                                        <div>
                                            <p class="text-xs text-slate-400">Full Name</p>
                                            <p class="text-sm text-slate-800 dark:text-slate-200">{{ $user->requester->full_name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-400">Phone</p>
                                            <p class="text-sm text-slate-800 dark:text-slate-200">{{ $user->requester->phone ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-400">Company</p>
                                            <p class="text-sm text-slate-800 dark:text-slate-200">{{ $user->requester->company ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="p-5 border-t border-slate-100 dark:border-slate-800 space-y-2">
                            <a href="{{ route('users.edit', $user) }}"
                                class="flex items-center justify-center gap-2 w-full px-4 py-2.5 btn-accent text-white text-sm font-semibold rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit User
                            </a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST"
                                    onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-red-50 hover:bg-red-600 dark:bg-red-950/40 dark:hover:bg-red-600 text-red-600 hover:text-white dark:text-red-400 dark:hover:text-white text-sm font-semibold rounded-lg transition-colors border border-red-200 dark:border-red-900 hover:border-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete User
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="lg:col-span-2 space-y-5">

                    <!-- Activity Stats -->
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Activity Stats</h3>
                        </div>
                        <div class="p-5 grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @if($user->role === 'requester' && $user->requester)
                                @php
                                    $statCards = [
                                        ['label' => 'Total',       'value' => $user->requester->tickets()->count(),                                    'color' => 'blue'],
                                        ['label' => 'Resolved',    'value' => $user->requester->tickets()->whereIn('status', ['resolved','closed'])->count(), 'color' => 'emerald'],
                                        ['label' => 'Open',        'value' => $user->requester->tickets()->where('status', 'open')->count(),            'color' => 'yellow'],
                                        ['label' => 'In Progress', 'value' => $user->requester->tickets()->where('status', 'in_progress')->count(),    'color' => 'purple'],
                                    ];
                                @endphp
                            @else
                                @php
                                    $statCards = [
                                        ['label' => 'Assigned',    'value' => $user->assignedTickets()->count(),                                        'color' => 'blue'],
                                        ['label' => 'Resolved',    'value' => $user->assignedTickets()->whereIn('status', ['resolved','closed'])->count(), 'color' => 'emerald'],
                                        ['label' => 'Open',        'value' => $user->assignedTickets()->where('status', 'open')->count(),               'color' => 'yellow'],
                                        ['label' => 'In Progress', 'value' => $user->assignedTickets()->where('status', 'in_progress')->count(),        'color' => 'purple'],
                                    ];
                                @endphp
                            @endif

                            @php
                                $statColorMap = [
                                    'blue'   => 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300',
                                    'emerald'=> 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300',
                                    'yellow' => 'bg-yellow-50 dark:bg-yellow-950/40 text-yellow-700 dark:text-yellow-300',
                                    'purple' => 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300',
                                ];
                            @endphp

                            @foreach($statCards as $card)
                                <div class="p-4 {{ $statColorMap[$card['color']] }} rounded-xl text-center">
                                    <p class="text-2xl font-bold">{{ $card['value'] }}</p>
                                    <p class="text-xs font-medium mt-0.5 opacity-80">{{ $card['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Assigned / Submitted Tickets -->
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                {{ $user->role === 'requester' ? 'Submitted Tickets' : 'Assigned Tickets' }}
                            </h3>
                            <a href="{{ route('tickets.index') }}"
                                class="text-xs font-medium text-accent hover:underline transition-colors">
                                View all →
                            </a>
                        </div>

                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                            @php
                                $tickets = $user->role === 'requester' && $user->requester
                                    ? $user->requester->tickets()->with(['category', 'assignedUser'])->latest()->take(5)->get()
                                    : $user->assignedTickets()->with(['requester', 'category'])->latest()->take(5)->get();
                            @endphp

                            @forelse($tickets as $ticket)
                                <a href="{{ route('tickets.show', $ticket) }}"
                                    class="flex items-center justify-between px-5 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-mono text-slate-400">#{{ $ticket->id }}</span>
                                            <p class="text-sm font-medium text-slate-800 dark:text-slate-100 truncate group-hover:text-accent transition-colors">
                                                {{ $ticket->subject }}
                                            </p>
                                        </div>
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            {{ $ticket->category->name }} · {{ $ticket->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <span class="ml-4 flex-shrink-0 px-2 py-0.5 text-xs font-semibold rounded-full {{ $ticket->status_badge_class }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </a>
                            @empty
                                <div class="flex flex-col items-center justify-center py-10 px-5">
                                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">No tickets yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>