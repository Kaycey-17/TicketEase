<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome Banner --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl px-6 py-6 shadow-lg">
                <div class="absolute -top-8 -right-8 w-40 h-40 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-10 -right-24 w-56 h-56 bg-white/5 rounded-full"></div>
                <div class="relative">
                    <p class="text-blue-200 text-sm font-medium mb-1">{{ now()->format('l, F j, Y') }}</p>
                    <h2 class="text-2xl font-bold text-white mb-1">Welcome back, {{ auth()->user()->name }}! 👋</h2>
                    <p class="text-blue-200 text-sm">
                        @if(auth()->user()->isAdmin()) You have full system access. Here's what's happening today.
                        @elseif(auth()->user()->isSupervisor()) You're monitoring the support team. Here's today's overview.
                        @elseif(auth()->user()->isAgent()) Here are your assigned tickets. Let's resolve some today!
                        @elseif(auth()->user()->isRequester()) Track your support requests and stay updated on progress.
                        @endif
                    </p>
                </div>
                <div class="relative mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('tickets.create') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-blue-700 text-sm font-semibold rounded-lg hover:bg-blue-50 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Ticket
                    </a>
                    <a href="{{ route('tickets.index') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-700/50 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors border border-white/20">
                        View All Tickets
                    </a>
                    @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                        <a href="{{ route('reports.index') }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-700/50 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors border border-white/20">
                            Reports
                        </a>
                    @endif
                </div>
            </div>

            {{-- No profile warning --}}
            @if(auth()->user()->isRequester() && !auth()->user()->requester)
                <div class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-amber-800 font-medium">Your account has no requester profile linked. Please contact an administrator.</p>
                </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @php
                    $statCards = [
                        ['label'=>'Total',       'value'=>$stats['total_tickets'],       'color'=>'blue',    'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['label'=>'Open',        'value'=>$stats['open_tickets'],        'color'=>'yellow',  'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label'=>'In Progress', 'value'=>$stats['in_progress_tickets'], 'color'=>'purple',  'icon'=>'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                        ['label'=>'Pending',     'value'=>$stats['pending_tickets'],     'color'=>'orange',  'icon'=>'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label'=>'Resolved',    'value'=>$stats['resolved_tickets'],    'color'=>'emerald', 'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ];
                    $colorMap = [
                        'blue'    => ['bg'=>'bg-blue-50',    'icon'=>'text-blue-600',    'num'=>'text-blue-700',    'border'=>'border-blue-100'],
                        'yellow'  => ['bg'=>'bg-yellow-50',  'icon'=>'text-yellow-600',  'num'=>'text-yellow-700',  'border'=>'border-yellow-100'],
                        'purple'  => ['bg'=>'bg-purple-50',  'icon'=>'text-purple-600',  'num'=>'text-purple-700',  'border'=>'border-purple-100'],
                        'orange'  => ['bg'=>'bg-orange-50',  'icon'=>'text-orange-600',  'num'=>'text-orange-700',  'border'=>'border-orange-100'],
                        'emerald' => ['bg'=>'bg-emerald-50', 'icon'=>'text-emerald-600', 'num'=>'text-emerald-700', 'border'=>'border-emerald-100'],
                    ];
                @endphp
                @foreach($statCards as $card)
                    @php $c = $colorMap[$card['color']]; @endphp
                    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $card['label'] }}</p>
                            <div class="p-1.5 {{ $c['bg'] }} rounded-lg border {{ $c['border'] }}">
                                <svg class="w-4 h-4 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold {{ $c['num'] }}">{{ $card['value'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 {{ (auth()->user()->isAdmin() || auth()->user()->isSupervisor()) ? 'lg:grid-cols-2' : '' }} gap-6">

                <!-- Recent Tickets -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-800">
                            @if(auth()->user()->isAgent()) My Assigned Tickets
                            @elseif(auth()->user()->isRequester()) My Recent Tickets
                            @else Recent Tickets
                            @endif
                        </h3>
                        <a href="{{ route('tickets.index') }}" class="text-xs font-medium text-accent hover:underline">View all →</a>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($recentTickets as $ticket)
                            <a href="{{ route('tickets.show', $ticket) }}"
                                class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 transition-colors group">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-xs font-mono text-slate-400">#{{ $ticket->id }}</span>
                                        <p class="text-sm font-medium text-slate-800 truncate group-hover:text-accent transition-colors">
                                            {{ Str::limit($ticket->subject, 40) }}
                                        </p>
                                    </div>
                                    <p class="text-xs text-slate-400">
                                        @if(!auth()->user()->isRequester()) {{ $ticket->requester->full_name }} · @endif
                                        {{ $ticket->category->name }} · {{ $ticket->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 ml-4 flex-shrink-0">
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $ticket->status_badge_class }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $ticket->priority_badge_class }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="flex flex-col items-center justify-center py-12 px-6">
                                <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-slate-500 mb-2">No tickets yet</p>
                                <a href="{{ route('tickets.create') }}" class="text-sm text-accent font-medium hover:underline">Create your first ticket →</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Activity --}}
                @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100">
                            <h3 class="text-sm font-semibold text-slate-800">Recent Activity</h3>
                            <a href="{{ route('activity_logs.index') }}" class="text-xs font-medium text-accent hover:underline">View all →</a>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse($recentActivity as $log)
                                <div class="flex items-start gap-3 px-6 py-4">
                                    <div class="h-8 w-8 rounded-full btn-accent flex-shrink-0 flex items-center justify-center text-white text-xs font-bold">
                                        {{ $log->user ? substr($log->user->name, 0, 2) : 'SY' }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="text-sm font-medium text-slate-800">{{ $log->user->name ?? 'System' }}</p>
                                            <span class="px-2 py-0.5 text-xs rounded-full {{ $log->action_color }}">
                                                {{ $log->action_icon }} {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $log->description }}</p>
                                    </div>
                                    <p class="text-xs text-slate-400 whitespace-nowrap flex-shrink-0">{{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            @empty
                                <div class="flex flex-col items-center justify-center py-12 px-6">
                                    <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-slate-500">No activity yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>