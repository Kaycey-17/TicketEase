<x-app-layout>
    <x-slot name="header">Edit Ticket #{{ $ticket->id }}</x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-5">
                <a href="{{ route('tickets.show', $ticket) }}"
                    class="inline-flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-100 transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Ticket
                </a>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">

                {{-- Header --}}
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Edit Ticket</h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            <span class="font-mono">#{{ $ticket->id }}</span> — {{ Str::limit($ticket->subject, 50) }}
                        </p>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $ticket->status_badge_class }}">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </div>

                {{-- Requester notice --}}
                @if(auth()->user()->isRequester())
                    <div class="mx-6 mt-5 flex items-center gap-2 p-3 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 rounded-lg">
                        <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-blue-800 dark:text-blue-300 font-medium">
                            You can only edit the subject and description of your ticket.
                        </p>
                    </div>
                @endif

                {{-- Agent notice --}}
                @if(auth()->user()->isAgent())
                    <div class="mx-6 mt-5 flex items-center gap-2 p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 rounded-lg">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-amber-800 dark:text-amber-300 font-medium">
                            As an agent, you can only update the status and assignment of this ticket.
                        </p>
                    </div>
                @endif

                <form action="{{ route('tickets.update', $ticket) }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- ══════════════════════════════════════════════════
                         AGENT VIEW — status + assigned_user_id only
                         ══════════════════════════════════════════════════ --}}
                    @if(auth()->user()->isAgent())

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            {{-- Status --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select name="status" required
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-400 @enderror">
                                    <option value="{{ $ticket->status }}" selected>
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }} (Current)
                                    </option>
                                    @foreach($ticket->getAllowedStatusTransitions() as $status)
                                        <option value="{{ $status }}" {{ old('status') === $status ? 'selected' : '' }}>
                                            → {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                @if(count($ticket->getAllowedStatusTransitions()) === 0)
                                    <p class="mt-1 text-xs text-slate-400">No further transitions allowed.</p>
                                @endif
                            </div>

                            {{-- Assigned User --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                    Assigned To
                                </label>
                                <select name="assigned_user_id"
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Unassigned</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}"
                                            {{ old('assigned_user_id', $ticket->assigned_user_id) == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }} ({{ ucfirst(str_replace('_', ' ', $agent->role)) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Read-only info for agent context --}}
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-100 dark:border-slate-700 space-y-2">
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-3">Ticket Info (read-only)</p>
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div>
                                    <p class="text-slate-400">Subject</p>
                                    <p class="text-slate-700 dark:text-slate-300 font-medium mt-0.5">{{ $ticket->subject }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Priority</p>
                                    <p class="mt-0.5">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $ticket->priority_badge_class }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Requester</p>
                                    <p class="text-slate-700 dark:text-slate-300 font-medium mt-0.5">{{ $ticket->requester->full_name }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Category</p>
                                    <p class="text-slate-700 dark:text-slate-300 font-medium mt-0.5">{{ $ticket->category->name }}</p>
                                </div>
                            </div>
                        </div>

                    {{-- ══════════════════════════════════════════════════
                         REQUESTER VIEW — subject + description only
                         ══════════════════════════════════════════════════ --}}
                    @elseif(auth()->user()->isRequester())

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                Subject <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="subject" value="{{ old('subject', $ticket->subject) }}" required
                                class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('subject') border-red-400 @enderror">
                            @error('subject')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" rows="6" required
                                class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none @error('description') border-red-400 @enderror">{{ old('description', $ticket->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    {{-- ══════════════════════════════════════════════════
                         ADMIN / SUPERVISOR VIEW — full form
                         ══════════════════════════════════════════════════ --}}
                    @else

                        {{-- Requester --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                Requester <span class="text-red-500">*</span>
                            </label>
                            <select name="requester_id" required
                                class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('requester_id') border-red-400 @enderror">
                                <option value="">Select Requester...</option>
                                @foreach($requesters as $requester)
                                    <option value="{{ $requester->id }}"
                                        {{ old('requester_id', $ticket->requester_id) == $requester->id ? 'selected' : '' }}>
                                        {{ $requester->full_name }} ({{ $requester->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('requester_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Category / Status / Priority --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select name="category_id" required
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('category_id') border-red-400 @enderror">
                                    <option value="">Select...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $ticket->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select name="status" required
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-400 @enderror">
                                    <option value="{{ $ticket->status }}" selected>
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }} (Current)
                                    </option>
                                    @foreach($ticket->getAllowedStatusTransitions() as $status)
                                        <option value="{{ $status }}" {{ old('status') === $status ? 'selected' : '' }}>
                                            → {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                @if(count($ticket->getAllowedStatusTransitions()) === 0)
                                    <p class="mt-1 text-xs text-slate-400">No further transitions.</p>
                                @endif
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                    Priority <span class="text-red-500">*</span>
                                </label>
                                <select name="priority" required
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('priority') border-red-400 @enderror">
                                    <option value="low"      {{ old('priority', $ticket->priority) === 'low'      ? 'selected' : '' }}>Low</option>
                                    <option value="medium"   {{ old('priority', $ticket->priority) === 'medium'   ? 'selected' : '' }}>Medium</option>
                                    <option value="high"     {{ old('priority', $ticket->priority) === 'high'     ? 'selected' : '' }}>High</option>
                                    <option value="critical" {{ old('priority', $ticket->priority) === 'critical' ? 'selected' : '' }}>Critical</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Subject --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                Subject <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="subject" value="{{ old('subject', $ticket->subject) }}" required
                                class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('subject') border-red-400 @enderror">
                            @error('subject')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" rows="6" required
                                class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none @error('description') border-red-400 @enderror">{{ old('description', $ticket->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Assign To --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Assign To</label>
                            <select name="assigned_user_id"
                                class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Unassigned</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}"
                                        {{ old('assigned_user_id', $ticket->assigned_user_id) == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }} ({{ ucfirst(str_replace('_', ' ', $agent->role)) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    @endif

                    {{-- Buttons --}}
                    <div class="flex gap-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                            Update Ticket
                        </button>
                        <a href="{{ route('tickets.show', $ticket) }}"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg transition-colors">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>