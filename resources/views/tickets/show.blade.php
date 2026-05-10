<x-app-layout>
    <x-slot name="header">Ticket #{{ $ticket->id }}</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Back button --}}
            <div class="mb-5">
                <a href="{{ route('tickets.index') }}"
                    class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Tickets
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-5">

                    <!-- Ticket Details Card -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <!-- Header bar with status color accent -->
                        <div class="h-1.5 w-full
                            @if($ticket->status === 'open') bg-blue-500
                            @elseif($ticket->status === 'in_progress') bg-purple-500
                            @elseif($ticket->status === 'pending') bg-yellow-500
                            @elseif($ticket->status === 'resolved') bg-emerald-500
                            @elseif($ticket->status === 'closed') bg-slate-400
                            @endif">
                        </div>

                        <div class="p-6">
                            <div class="flex justify-between items-start gap-4 mb-5">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-xs font-mono font-semibold text-slate-400">#{{ $ticket->id }}</span>
                                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $ticket->status_badge_class }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $ticket->priority_badge_class }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                        @if($ticket->status === 'closed')
                                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-500">
                                                🔒 Locked
                                            </span>
                                        @endif
                                    </div>
                                    <h2 class="text-xl font-bold text-slate-900 leading-snug">{{ $ticket->subject }}</h2>
                                    <p class="text-xs text-slate-400 mt-1">
                                        Created {{ $ticket->created_at->format('M d, Y \a\t h:i A') }}
                                    </p>
                                </div>

                                {{-- Edit button --}}
                                @if(!auth()->user()->isRequester() && $ticket->status !== 'closed')
                                    <a href="{{ route('tickets.edit', $ticket) }}"
                                        class="flex-shrink-0 inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                @elseif(auth()->user()->isRequester() && !in_array($ticket->status, ['resolved', 'closed']))
                                    <a href="{{ route('tickets.edit', $ticket) }}"
                                        class="flex-shrink-0 inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                @endif
                            </div>

                            <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $ticket->description }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Replies -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100">
                            <h3 class="text-sm font-semibold text-slate-800">
                                Conversation
                                <span class="ml-1.5 text-xs font-normal text-slate-400">
                                    ({{ $ticket->replies->filter(fn($r) => !$r->is_internal || !auth()->user()->isRequester())->count() }} messages)
                                </span>
                            </h3>
                        </div>

                        <div class="p-6 space-y-4">
                            @forelse($ticket->replies->filter(function($reply) {
                                return !$reply->is_internal || !auth()->user()->isRequester();
                            }) as $reply)
                                <div class="flex gap-3">
                                    <div class="h-8 w-8 rounded-full flex-shrink-0 flex items-center justify-center text-white text-xs font-bold shadow-sm
                                        {{ $reply->is_internal ? 'bg-amber-500' : 'bg-blue-500' }}">
                                        {{ substr($reply->user->name, 0, 2) }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <p class="text-sm font-semibold text-slate-800">{{ $reply->user->name }}</p>
                                            @if($reply->is_internal)
                                                <span class="px-2 py-0.5 text-xs bg-amber-100 text-amber-700 rounded-full font-medium">
                                                    Internal Note
                                                </span>
                                            @endif
                                            <span class="text-xs text-slate-400">{{ $reply->created_at->format('M d, Y h:i A') }}</span>

                                            @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                                <form action="{{ route('tickets.replies.destroy', [$ticket, $reply]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Delete this reply?');"
                                                    class="ml-auto">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-xs text-red-400 hover:text-red-600 transition-colors">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                        <div class="rounded-lg p-3 text-sm text-slate-700 leading-relaxed
                                            {{ $reply->is_internal ? 'bg-amber-50 border border-amber-100' : 'bg-slate-50 border border-slate-100' }}">
                                            {{ $reply->message }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-slate-500">No replies yet. Be the first to respond.</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Reply form --}}
                        @if($ticket->status !== 'closed')
                            <div class="px-6 pb-6 border-t border-slate-100 pt-5">
                                <form action="{{ route('tickets.replies.store', $ticket) }}" method="POST">
                                    @csrf
                                    <label class="block text-xs font-semibold text-slate-600 mb-2">Add Reply</label>
                                    <textarea name="message" rows="4" required
                                        placeholder="Write your reply here..."
                                        class="w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400 resize-none mb-3"></textarea>

                                    <div class="flex items-center justify-between gap-3">
                                        @if(!auth()->user()->isRequester())
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="is_internal"
                                                    class="rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                                                <span class="text-xs text-slate-600 font-medium">
                                                    Internal note (hidden from requester)
                                                </span>
                                            </label>
                                        @else
                                            <div></div>
                                        @endif

                                        <button type="submit"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                            </svg>
                                            Send Reply
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                                <p class="text-sm text-slate-400 text-center">
                                    🔒 This ticket is closed — no further replies can be added.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-5">

                    <!-- Ticket Info -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                            <h3 class="text-sm font-semibold text-slate-800">Ticket Details</h3>
                        </div>
                        <div class="p-5 space-y-4">

                            <div>
                                <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Status</p>
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $ticket->status_badge_class }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </div>

                            <div>
                                <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Priority</p>
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $ticket->priority_badge_class }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </div>

                            <div>
                                <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Category</p>
                                <p class="text-sm text-slate-800 font-medium">{{ $ticket->category->name }}</p>
                            </div>

                            <div>
                                <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Requester</p>
                                <p class="text-sm text-slate-800 font-medium">{{ $ticket->requester->full_name }}</p>
                                <p class="text-xs text-slate-400">{{ $ticket->requester->email }}</p>
                            </div>

                            <div>
                                <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Assigned To</p>
                                @if($ticket->assignedUser)
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold">
                                            {{ substr($ticket->assignedUser->name, 0, 1) }}
                                        </div>
                                        <p class="text-sm text-slate-800 font-medium">{{ $ticket->assignedUser->name }}</p>
                                    </div>
                                @else
                                    <p class="text-sm text-slate-400 italic">Unassigned</p>
                                @endif
                            </div>

                            @if(!auth()->user()->isRequester())
                                <div>
                                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Created By</p>
                                    <p class="text-sm text-slate-800 font-medium">{{ $ticket->creator->name }}</p>
                                </div>
                            @endif

                            @if($ticket->resolved_at)
                                <div>
                                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Resolved At</p>
                                    <p class="text-sm text-slate-800">{{ $ticket->resolved_at->format('M d, Y h:i A') }}</p>
                                </div>
                            @endif

                        </div>
                    </div>

                    <!-- Quick Actions — staff only -->
                    @if(!auth()->user()->isRequester())
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                                <h3 class="text-sm font-semibold text-slate-800">Quick Actions</h3>
                            </div>
                            <div class="p-5 space-y-2">

                                @if($ticket->status !== 'closed')
                                    <a href="{{ route('tickets.edit', $ticket) }}"
                                        class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Ticket
                                    </a>
                                @else
                                    <div class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-slate-100 text-slate-400 text-sm font-medium rounded-lg cursor-not-allowed">
                                        🔒 Ticket Closed
                                    </div>
                                @endif

                                @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <form action="{{ route('tickets.destroy', $ticket) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this ticket? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-sm font-semibold rounded-lg transition-colors border border-red-200 hover:border-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete Ticket
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Status History -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                            <h3 class="text-sm font-semibold text-slate-800">Status History</h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $ticket->statusHistories->count() }} change{{ $ticket->statusHistories->count() !== 1 ? 's' : '' }}
                            </p>
                        </div>

                        <div class="p-5">
                            @if($ticket->statusHistories->count() > 0)
                                <div class="relative">
                                    {{-- Timeline line --}}
                                    <div class="absolute left-3.5 top-0 bottom-0 w-px bg-slate-200"></div>

                                    <div class="space-y-4">
                                        @foreach($ticket->statusHistories as $history)
                                            <div class="flex gap-3 relative">

                                                {{-- Timeline dot --}}
                                                <div class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs z-10
                                                    @if($history->to_status === 'open') bg-blue-500
                                                    @elseif($history->to_status === 'in_progress') bg-purple-500
                                                    @elseif($history->to_status === 'pending') bg-yellow-500
                                                    @elseif($history->to_status === 'resolved') bg-emerald-500
                                                    @elseif($history->to_status === 'closed') bg-slate-500
                                                    @endif text-white shadow-sm">
                                                    @if($history->to_status === 'open') 🔵
                                                    @elseif($history->to_status === 'in_progress') 🟣
                                                    @elseif($history->to_status === 'pending') 🟡
                                                    @elseif($history->to_status === 'resolved') 🟢
                                                    @elseif($history->to_status === 'closed') ⚫
                                                    @endif
                                                </div>

                                                {{-- Content --}}
                                                <div class="flex-1 min-w-0 pb-1">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        {{-- From status --}}
                                                        @if($history->from_status)
                                                            <span class="px-2 py-0.5 text-xs rounded-full
                                                                @if($history->from_status === 'open') bg-blue-100 text-blue-700
                                                                @elseif($history->from_status === 'in_progress') bg-purple-100 text-purple-700
                                                                @elseif($history->from_status === 'pending') bg-yellow-100 text-yellow-700
                                                                @elseif($history->from_status === 'resolved') bg-emerald-100 text-emerald-700
                                                                @elseif($history->from_status === 'closed') bg-slate-100 text-slate-600
                                                                @endif font-semibold">
                                                                {{ ucfirst(str_replace('_', ' ', $history->from_status)) }}
                                                            </span>

                                                            {{-- Arrow --}}
                                                            <svg class="w-3 h-3 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                            </svg>
                                                        @endif

                                                        {{-- To status --}}
                                                        <span class="px-2 py-0.5 text-xs rounded-full font-semibold
                                                            @if($history->to_status === 'open') bg-blue-100 text-blue-700
                                                            @elseif($history->to_status === 'in_progress') bg-purple-100 text-purple-700
                                                            @elseif($history->to_status === 'pending') bg-yellow-100 text-yellow-700
                                                            @elseif($history->to_status === 'resolved') bg-emerald-100 text-emerald-700
                                                            @elseif($history->to_status === 'closed') bg-slate-100 text-slate-600
                                                            @endif">
                                                            {{ ucfirst(str_replace('_', ' ', $history->to_status)) }}
                                                        </span>
                                                    </div>

                                                    {{-- Who changed it --}}
                                                    <p class="text-xs text-slate-500 mt-1">
                                                        by <span class="font-medium text-slate-700">
                                                            {{ $history->changedBy->name ?? 'System' }}
                                                        </span>
                                                    </p>

                                                    {{-- Time --}}
                                                    <p class="text-xs text-slate-400 mt-0.5">
                                                        {{ $history->created_at->format('M d, Y h:i A') }}
                                                        <span class="text-slate-300">·</span>
                                                        {{ $history->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-xs text-slate-400">No status changes yet</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>