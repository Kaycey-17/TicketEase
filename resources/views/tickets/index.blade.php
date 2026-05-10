<x-app-layout>
    <x-slot name="header">Tickets</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <!-- Filters + Create -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-800">Filter Tickets</h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $tickets->total() }} ticket{{ $tickets->total() !== 1 ? 's' : '' }} found
                        </p>
                    </div>
                    <a href="{{ route('tickets.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Ticket
                    </a>
                </div>

                <form method="GET" action="{{ route('tickets.index') }}"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Subject, ID..."
                            class="w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Status</label>
                        <select name="status"
                            class="w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            <option value="open"        {{ request('status') === 'open'        ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="pending"     {{ request('status') === 'pending'     ? 'selected' : '' }}>Pending</option>
                            <option value="resolved"    {{ request('status') === 'resolved'    ? 'selected' : '' }}>Resolved</option>
                            <option value="closed"      {{ request('status') === 'closed'      ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Priority</label>
                        <select name="priority"
                            class="w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Priorities</option>
                            <option value="low"      {{ request('priority') === 'low'      ? 'selected' : '' }}>Low</option>
                            <option value="medium"   {{ request('priority') === 'medium'   ? 'selected' : '' }}>Medium</option>
                            <option value="high"     {{ request('priority') === 'high'     ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Category</label>
                        <select name="category_id"
                            class="w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4 flex gap-2">
                        <button type="submit"
                            class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors">
                            Apply Filters
                        </button>
                        <a href="{{ route('tickets.index') }}"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tickets Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ID</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Subject</th>
                                @if(!auth()->user()->isRequester())
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Requester</th>
                                @endif
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Priority</th>
                                @if(!auth()->user()->isRequester())
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Assigned To</th>
                                @endif
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Created</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($tickets as $ticket)
                                <tr class="hover:bg-blue-50/40 transition-colors group">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="text-xs font-mono font-semibold text-slate-400">#{{ $ticket->id }}</span>
                                    </td>
                                    <td class="px-5 py-4 max-w-xs">
                                        <a href="{{ route('tickets.show', $ticket) }}"
                                            class="text-sm font-medium text-slate-800 hover:text-blue-700 transition-colors line-clamp-1">
                                            {{ Str::limit($ticket->subject, 50) }}
                                        </a>
                                    </td>
                                    @if(!auth()->user()->isRequester())
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            <span class="text-sm text-slate-600">{{ $ticket->requester->full_name }}</span>
                                        </td>
                                    @endif
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="text-sm text-slate-500">{{ $ticket->category->name }}</span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $ticket->status_badge_class }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $ticket->priority_badge_class }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                    @if(!auth()->user()->isRequester())
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            @if($ticket->assignedUser)
                                                <div class="flex items-center gap-2">
                                                    <div class="h-6 w-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold">
                                                        {{ substr($ticket->assignedUser->name, 0, 1) }}
                                                    </div>
                                                    <span class="text-sm text-slate-600">{{ $ticket->assignedUser->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400 italic">Unassigned</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div>
                                            <p class="text-sm text-slate-600">{{ $ticket->created_at->format('M d, Y') }}</p>
                                            <p class="text-xs text-slate-400">{{ $ticket->created_at->diffForHumans() }}</p>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <a href="{{ route('tickets.show', $ticket) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg transition-colors">
                                            View
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->isRequester() ? '6' : '9' }}"
                                        class="px-5 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm font-medium text-slate-500">No tickets found</p>
                                            <p class="text-xs text-slate-400 mt-1">Try adjusting your filters</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-5 py-4 border-t border-slate-100 bg-slate-50">
                    {{ $tickets->appends(request()->all())->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>