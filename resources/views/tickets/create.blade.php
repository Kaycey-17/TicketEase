{{--
    SHARED FORM PARTIAL STYLES
    Applied across: tickets/create, tickets/edit, categories/create, categories/edit,
    requesters/create, requesters/edit, users/create, users/edit
--}}

{{-- ============================================================
     tickets/create.blade.php
     ============================================================ --}}
<x-app-layout>
    <x-slot name="header">Create New Ticket</x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-5">
                <a href="{{ route('tickets.index') }}"
                    class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Tickets
                </a>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-base font-semibold text-slate-800">Ticket Information</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Fill in the details below to submit a new support ticket.</p>
                </div>

                <form action="{{ route('tickets.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf

                    {{-- Requester selector — staff only --}}
                    @if(!auth()->user()->isRequester())
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Requester <span class="text-red-500">*</span></label>
                            <select name="requester_id" required
                                class="w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('requester_id') border-red-400 @enderror">
                                <option value="">Select Requester...</option>
                                @foreach($requesters as $requester)
                                    <option value="{{ $requester->id }}" {{ old('requester_id') == $requester->id ? 'selected' : '' }}>
                                        {{ $requester->full_name }} ({{ $requester->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('requester_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @else
                        @if(auth()->user()->requester)
                            <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ substr(auth()->user()->requester->full_name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-blue-800">Submitting as</p>
                                    <p class="text-sm text-blue-700">{{ auth()->user()->requester->full_name }}</p>
                                </div>
                            </div>
                        @else
                            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-700 font-medium">⚠️ No requester profile linked. Contact an administrator.</p>
                            </div>
                        @endif
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Category -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Category <span class="text-red-500">*</span></label>
                            <select name="category_id" required
                                class="w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('category_id') border-red-400 @enderror">
                                <option value="">Select Category...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Priority <span class="text-red-500">*</span></label>
                            <select name="priority" required
                                class="w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('priority') border-red-400 @enderror">
                                <option value="low"    {{ old('priority') === 'low'    ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high"   {{ old('priority') === 'high'   ? 'selected' : '' }}>High</option>
                                <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                            @error('priority')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Subject <span class="text-red-500">*</span></label>
                        <input type="text" name="subject" value="{{ old('subject') }}" required
                            placeholder="Brief summary of the issue..."
                            class="w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('subject') border-red-400 @enderror">
                        @error('subject')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="6" required
                            placeholder="Describe the issue in detail..."
                            class="w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Assign To — staff only --}}
                    @if(!auth()->user()->isRequester())
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Assign To <span class="text-slate-400 font-normal">(optional)</span></label>
                            <select name="assigned_user_id"
                                class="w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Leave Unassigned</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ old('assigned_user_id') == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }} ({{ ucfirst(str_replace('_', ' ', $agent->role)) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="flex gap-3 pt-2 border-t border-slate-100">
                        <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                            Create Ticket
                        </button>
                        <a href="{{ route('tickets.index') }}"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>