<x-app-layout>
    <x-slot name="header">
        Requester Details
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Back button --}}
            <div class="mb-4">
                <a href="{{ route('requesters.index') }}"
                    class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Requesters
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Requester Information -->
                <div class="lg:col-span-1 space-y-6">

                    <!-- Info Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h2 class="text-xl font-bold text-gray-900">Requester Information</h2>
                            <a href="{{ route('requesters.edit', $requester) }}"
                                class="text-blue-600 hover:text-blue-800 text-sm">
                                Edit
                            </a>
                        </div>

                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $requester->full_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $requester->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $requester->phone ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Company</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $requester->company ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $requester->created_at->format('M d, Y') }}
                                </dd>
                            </div>

                            {{-- Show linked user account if exists --}}
                            @if($requester->user)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Login Account</dt>
                                    <dd class="mt-1 text-sm text-green-600">✅ Has login account</dd>
                                </div>
                            @else
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Login Account</dt>
                                    <dd class="mt-1 text-sm text-gray-400">No login account</dd>
                                </div>
                            @endif
                        </dl>

                        {{-- Delete — admin and supervisor only --}}
                        @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                            <form action="{{ route('requesters.destroy', $requester) }}"
                                method="POST" class="mt-6"
                                onsubmit="return confirm('Are you sure you want to delete this requester?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                                    Delete Requester
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Ticket Stats Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ticket Summary</h3>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                <span class="text-sm text-blue-700">Total</span>
                                <span class="font-bold text-blue-900">{{ $stats['total'] }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                                <span class="text-sm text-yellow-700">Open</span>
                                <span class="font-bold text-yellow-900">{{ $stats['open'] }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                                <span class="text-sm text-purple-700">In Progress</span>
                                <span class="font-bold text-purple-900">{{ $stats['in_progress'] }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg">
                                <span class="text-sm text-orange-700">Pending</span>
                                <span class="font-bold text-orange-900">{{ $stats['pending'] }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                <span class="text-sm text-green-700">Resolved</span>
                                <span class="font-bold text-green-900">{{ $stats['resolved'] }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tickets List -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Tickets</h3>
                            <span class="text-sm text-gray-500">
                                {{ $stats['total'] }} total
                            </span>
                        </div>

                        <div class="space-y-3">
                            @forelse($tickets as $ticket)
                                <a href="{{ route('tickets.show', $ticket) }}"
                                    class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-gray-400">#{{ $ticket->id }}</span>
                                                <h4 class="font-medium text-gray-900">{{ $ticket->subject }}</h4>
                                            </div>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $ticket->category->name }}
                                                @if($ticket->assignedUser)
                                                    • Assigned to {{ $ticket->assignedUser->name }}
                                                @else
                                                    • Unassigned
                                                @endif
                                                • {{ $ticket->created_at->diffForHumans() }}
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
                                <p class="text-gray-500 text-center py-4">No tickets yet</p>
                            @endforelse
                        </div>

                        {{-- Pagination --}}
                        @if($tickets->hasPages())
                            <div class="mt-4 pt-4 border-t">
                                {{ $tickets->links() }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>