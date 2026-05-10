<x-app-layout>
    <x-slot name="header">Edit Requester</x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-5">
                <a href="{{ route('requesters.show', $requester) }}"
                    class="inline-flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-100 transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to {{ $requester->full_name }}
                </a>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">

                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Edit Requester</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Update information for {{ $requester->full_name }}.</p>
                </div>

                {{-- Agent restriction notice --}}
                @if(auth()->user()->isAgent())
                    <div class="mx-6 mt-5 flex items-center gap-2 p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 rounded-lg">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-amber-800 dark:text-amber-300 font-medium">
                            As an agent, you can only update the phone number and company of a requester.
                        </p>
                    </div>
                @endif

                <form action="{{ route('requesters.update', $requester) }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- ══════════════════════════════════════════════════
                         AGENT VIEW — phone + company only
                         ══════════════════════════════════════════════════ --}}
                    @if(auth()->user()->isAgent())

                        {{-- Read-only identity info --}}
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-100 dark:border-slate-700">
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-3">
                                Requester Identity (read-only)
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                <div>
                                    <p class="text-xs text-slate-400">First Name</p>
                                    <p class="text-slate-700 dark:text-slate-300 font-medium mt-0.5">{{ $requester->first_name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">Last Name</p>
                                    <p class="text-slate-700 dark:text-slate-300 font-medium mt-0.5">{{ $requester->last_name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">Email</p>
                                    <p class="text-slate-700 dark:text-slate-300 font-medium mt-0.5">{{ $requester->email }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Editable fields --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $requester->phone) }}"
                                    placeholder="+63 912 345 6789"
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('phone') border-red-400 @enderror">
                                @error('phone')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Company / Department</label>
                                <input type="text" name="company" value="{{ old('company', $requester->company) }}"
                                    placeholder="e.g. Acme Corp"
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('company') border-red-400 @enderror">
                                @error('company')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                    {{-- ══════════════════════════════════════════════════
                         ADMIN / SUPERVISOR VIEW — full form
                         ══════════════════════════════════════════════════ --}}
                    @else

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="first_name" value="{{ old('first_name', $requester->first_name) }}" required
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('first_name') border-red-400 @enderror">
                                @error('first_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="last_name" value="{{ old('last_name', $requester->last_name) }}" required
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('last_name') border-red-400 @enderror">
                                @error('last_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $requester->email) }}" required
                                class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $requester->phone) }}"
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('phone') border-red-400 @enderror">
                                @error('phone')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Company / Department</label>
                                <input type="text" name="company" value="{{ old('company', $requester->company) }}"
                                    class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('company') border-red-400 @enderror">
                                @error('company')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                    @endif

                    <div class="flex gap-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="submit"
                            class="px-5 py-2.5 btn-accent text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                            {{ auth()->user()->isAgent() ? 'Update Contact Info' : 'Update Requester' }}
                        </button>
                        <a href="{{ route('requesters.show', $requester) }}"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg transition-colors">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>