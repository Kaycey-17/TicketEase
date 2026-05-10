<x-app-layout>
    <x-slot name="header">
        My Profile
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Account Info --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">
                    Account Information
                </h3>

                <div class="flex items-center gap-4 mb-6">
                    <div class="h-16 w-16 rounded-full bg-blue-500 flex items-center justify-center text-white text-xl font-bold">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">
                            Requester
                        </span>
                    </div>
                </div>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ auth()->user()->requester->full_name ?? 'N/A' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ auth()->user()->email }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ auth()->user()->requester->phone ?? 'N/A' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Company</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ auth()->user()->requester->company ?? 'N/A' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ auth()->user()->created_at->format('M d, Y') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Account Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                Active
                            </span>
                        </dd>
                    </div>
                </dl>

                <div class="mt-4">
                    <a href="{{ route('profile.edit') }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                        Edit Account
                    </a>
                </div>
            </div>

            {{-- Ticket Stats --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">
                    My Ticket Summary
                </h3>

                @php
                    $requester  = auth()->user()->requester;
                    $total      = $requester ? $requester->tickets()->count() : 0;
                    $open       = $requester ? $requester->tickets()->where('status', 'open')->count() : 0;
                    $inProgress = $requester ? $requester->tickets()->where('status', 'in_progress')->count() : 0;
                    $pending    = $requester ? $requester->tickets()->where('status', 'pending')->count() : 0;
                    $resolved   = $requester ? $requester->tickets()->whereIn('status', ['resolved', 'closed'])->count() : 0;
                @endphp

                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                    <div class="p-4 bg-blue-50 rounded-lg text-center">
                        <p class="text-2xl font-bold text-blue-700">{{ $total }}</p>
                        <p class="text-sm text-blue-600 mt-1">Total</p>
                    </div>
                    <div class="p-4 bg-yellow-50 rounded-lg text-center">
                        <p class="text-2xl font-bold text-yellow-700">{{ $open }}</p>
                        <p class="text-sm text-yellow-600 mt-1">Open</p>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-lg text-center">
                        <p class="text-2xl font-bold text-purple-700">{{ $inProgress }}</p>
                        <p class="text-sm text-purple-600 mt-1">In Progress</p>
                    </div>
                    <div class="p-4 bg-orange-50 rounded-lg text-center">
                        <p class="text-2xl font-bold text-orange-700">{{ $pending }}</p>
                        <p class="text-sm text-orange-600 mt-1">Pending</p>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg text-center">
                        <p class="text-2xl font-bold text-green-700">{{ $resolved }}</p>
                        <p class="text-sm text-green-600 mt-1">Resolved</p>
                    </div>
                </div>

                <a href="{{ route('tickets.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                    View My Tickets
                </a>
            </div>

            {{-- Recent Tickets --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4 pb-2 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Tickets</h3>
                    <a href="{{ route('tickets.index') }}"
                        class="text-sm text-blue-600 hover:text-blue-700">
                        View all
                    </a>
                </div>

                @php
                    $recentTickets = $requester
                        ? $requester->tickets()->with('category')->latest()->take(5)->get()
                        : collect();
                @endphp

                <div class="space-y-3">
                    @forelse($recentTickets as $ticket)
                        <a href="{{ route('tickets.show', $ticket) }}"
                            class="block p-3 border rounded-lg hover:bg-gray-50 transition">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-400">#{{ $ticket->id }}</span>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ Str::limit($ticket->subject, 50) }}
                                        </p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $ticket->category->name }} •
                                        {{ $ticket->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-1 ml-4">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $ticket->status_badge_class }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $ticket->priority_badge_class }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-6">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-sm text-gray-500">No tickets yet</p>
                            <a href="{{ route('tickets.create') }}"
                                class="mt-1 inline-block text-sm text-blue-600 hover:text-blue-700">
                                Create your first ticket
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>