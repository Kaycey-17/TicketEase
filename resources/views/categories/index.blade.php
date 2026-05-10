<x-app-layout>
    <x-slot name="header">Categories</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Manage Categories</h2>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $categories->total() }} categories total</p>
                </div>
                <a href="{{ route('categories.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 btn-accent text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Category
                </a>
            </div>

            <!-- Categories Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($categories as $category)
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                        <div class="h-1 w-full {{ $category->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></div>
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">{{ $category->name }}</h3>
                                    <p class="text-xs text-slate-400 mt-0.5 line-clamp-2">
                                        {{ $category->description ?? 'No description provided.' }}
                                    </p>
                                </div>
                                <span class="ml-3 flex-shrink-0 px-2 py-0.5 text-xs font-semibold rounded-full
                                    {{ $category->is_active
                                        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
                                        : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 mb-4">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                                <span>
                                    <strong class="text-slate-700 dark:text-slate-300">{{ $category->tickets_count }}</strong>
                                    ticket{{ $category->tickets_count !== 1 ? 's' : '' }}
                                </span>
                            </div>

                            <div class="flex gap-2">
                                {{-- Edit — all staff can edit --}}
                                <a href="{{ route('categories.edit', $category) }}"
                                    class="flex-1 text-center px-3 py-2 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/40 dark:hover:bg-blue-900/60 text-blue-700 dark:text-blue-300 text-xs font-semibold rounded-lg transition-colors">
                                    Edit
                                </a>

                                {{-- Delete — admin and supervisor only, hidden from agents --}}
                                @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                        onsubmit="return confirm('Delete the \'{{ $category->name }}\' category? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-2 bg-red-50 hover:bg-red-100 dark:bg-red-950/40 dark:hover:bg-red-900/60 text-red-600 dark:text-red-400 text-xs font-semibold rounded-lg transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                @else
                                    {{-- Agents see a disabled placeholder so layout doesn't shift --}}
                                    <div class="px-3 py-2 bg-slate-50 dark:bg-slate-800 text-slate-300 dark:text-slate-600 text-xs font-semibold rounded-lg cursor-not-allowed"
                                        title="Only admins and supervisors can delete categories.">
                                        Delete
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-16 text-center">
                        <div class="w-14 h-14 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">No categories yet</p>
                        <a href="{{ route('categories.create') }}"
                            class="mt-2 inline-block text-sm text-accent font-medium hover:underline transition-colors">
                            Create your first category →
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div>{{ $categories->links() }}</div>

        </div>
    </div>
</x-app-layout>