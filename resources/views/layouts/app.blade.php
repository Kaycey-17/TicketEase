<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="{{ request()->cookie('dark_mode') === '1' ? 'dark' : '' }} {{ request()->cookie('compact_mode') === '1' ? 'compact' : '' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TicketEase') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @php
            // Resolve accent color from cookie
            $accent       = request()->cookie('accent_color', 'blue');
            $validAccents = ['blue', 'violet', 'emerald', 'rose', 'amber', 'slate'];
            if (!in_array($accent, $validAccents)) { $accent = 'blue'; }

            $palettes = [
                'blue'    => ['--accent-50'=>'#eff6ff','--accent-100'=>'#dbeafe','--accent-200'=>'#bfdbfe','--accent-400'=>'#60a5fa','--accent-500'=>'#3b82f6','--accent-600'=>'#2563eb','--accent-700'=>'#1d4ed8','--accent-900'=>'#1e3a5f','--accent-text'=>'#1d4ed8'],
                'violet'  => ['--accent-50'=>'#f5f3ff','--accent-100'=>'#ede9fe','--accent-200'=>'#ddd6fe','--accent-400'=>'#a78bfa','--accent-500'=>'#8b5cf6','--accent-600'=>'#7c3aed','--accent-700'=>'#6d28d9','--accent-900'=>'#2e1065','--accent-text'=>'#6d28d9'],
                'emerald' => ['--accent-50'=>'#ecfdf5','--accent-100'=>'#d1fae5','--accent-200'=>'#a7f3d0','--accent-400'=>'#34d399','--accent-500'=>'#10b981','--accent-600'=>'#059669','--accent-700'=>'#047857','--accent-900'=>'#064e3b','--accent-text'=>'#047857'],
                'rose'    => ['--accent-50'=>'#fff1f2','--accent-100'=>'#ffe4e6','--accent-200'=>'#fecdd3','--accent-400'=>'#fb7185','--accent-500'=>'#f43f5e','--accent-600'=>'#e11d48','--accent-700'=>'#be123c','--accent-900'=>'#4c0519','--accent-text'=>'#be123c'],
                'amber'   => ['--accent-50'=>'#fffbeb','--accent-100'=>'#fef3c7','--accent-200'=>'#fde68a','--accent-400'=>'#fbbf24','--accent-500'=>'#f59e0b','--accent-600'=>'#d97706','--accent-700'=>'#b45309','--accent-900'=>'#451a03','--accent-text'=>'#b45309'],
                'slate'   => ['--accent-50'=>'#f8fafc','--accent-100'=>'#f1f5f9','--accent-200'=>'#e2e8f0','--accent-400'=>'#94a3b8','--accent-500'=>'#64748b','--accent-600'=>'#475569','--accent-700'=>'#334155','--accent-900'=>'#0f172a','--accent-text'=>'#334155'],
            ];

            $cssVars = collect($palettes[$accent])->map(fn($v,$k) => "$k:$v")->implode(';');
        @endphp

        <style>
            /* Font */
            * { font-family: 'Plus Jakarta Sans', sans-serif; }

            /* Accent CSS variables — needed for dynamic color theming, Tailwind can't do runtime values */
            :root { {{ $cssVars }} }

            /* Accent utility classes used across views */
            .btn-accent       { background-color: var(--accent-600); color: #fff; }
            .btn-accent:hover { background-color: var(--accent-700); }
            .text-accent      { color: var(--accent-text); }
            .bg-accent-soft   { background-color: var(--accent-50); }
            .border-accent    { border-color: var(--accent-200); }
            .ring-accent      { --tw-ring-color: var(--accent-500); }

            /* Focus rings use accent */
            input:focus, select:focus, textarea:focus {
                --tw-ring-color: var(--accent-500) !important;
                border-color: var(--accent-500) !important;
                outline: none;
            }

            /* Compact mode — tighter spacing globally */
            .compact .py-8  { padding-top: 1.25rem !important; padding-bottom: 1.25rem !important; }
            .compact .py-6  { padding-top: 0.875rem !important; padding-bottom: 0.875rem !important; }
            .compact .py-5  { padding-top: 0.625rem !important; padding-bottom: 0.625rem !important; }
            .compact .py-4  { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
            .compact .px-6  { padding-left: 1rem !important; padding-right: 1rem !important; }
            .compact .px-5  { padding-left: 0.75rem !important; padding-right: 0.75rem !important; }
            .compact .gap-6 { gap: 1rem !important; }
            .compact .gap-5 { gap: 0.75rem !important; }
            .compact .space-y-6 > * + * { margin-top: 1rem !important; }
            .compact .space-y-5 > * + * { margin-top: 0.75rem !important; }
            .compact table td, .compact table th { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
            .compact .text-3xl { font-size: 1.5rem !important; }
            .compact .h-16    { height: 3.5rem !important; }

            /* Scrollbar — Tailwind doesn't support this */
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: #f1f5f9; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

            /* Animations — Tailwind doesn't have these specific ones */
            @keyframes slideDown {
                from { opacity: 0; transform: translateY(-8px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            .flash-message { animation: slideDown 0.3s ease; }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(6px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            main > * { animation: fadeIn 0.35s ease both; }

            @keyframes pulse-badge {
                0%, 100% { transform: scale(1); }
                50%       { transform: scale(1.2); }
            }
            .badge-pulse { animation: pulse-badge 2s ease-in-out infinite; }

            /* Bell dropdown scroll */
            .notif-dropdown { max-height: 420px; overflow-y: auto; }
        </style>
    </head>

    <body class="font-sans antialiased bg-slate-50">
        <div class="min-h-screen">

            @include('layouts.sidebar')

            <div class="lg:pl-64 flex flex-col min-h-screen">

                <!-- Top Header -->
                <div class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-sm">
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="flex h-16 items-center justify-between">

                            <!-- Mobile menu button -->
                            <button type="button" id="mobile-menu-button"
                                class="lg:hidden -m-2.5 p-2.5 text-slate-500 hover:text-slate-700 transition-colors">
                                <span class="sr-only">Open sidebar</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                </svg>
                            </button>

                            <!-- Page Heading -->
                            <div class="flex-1 ml-4 lg:ml-0">
                                @if (isset($header))
                                    <h1 class="text-lg font-semibold text-slate-800 tracking-tight">
                                        {{ $header }}
                                    </h1>
                                @endif
                            </div>

                            <!-- Right side -->
                            <div class="flex items-center gap-2">

                                <!-- Bell Notification (staff only) -->
                                @if(!auth()->user()->isRequester())
                                    <div class="relative" id="notification-wrapper">

                                        <button id="bell-btn" type="button"
                                            class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors"
                                            title="Notifications">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            </svg>
                                            <span id="bell-badge"
                                                class="absolute -top-0.5 -right-0.5 hidden h-4 min-w-4 px-1 items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold leading-none badge-pulse">
                                                0
                                            </span>
                                        </button>

                                        <!-- Bell Dropdown -->
                                        <div id="bell-dropdown"
                                            class="hidden absolute right-0 mt-2 w-80 rounded-xl bg-white shadow-xl ring-1 ring-slate-200 overflow-hidden z-50">

                                            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50">
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-800">
                                                        @if(auth()->user()->isAgent()) My Open Tickets
                                                        @else Unassigned Tickets
                                                        @endif
                                                    </p>
                                                    <p class="text-xs text-slate-400 mt-0.5">Needs attention</p>
                                                </div>
                                                <a href="{{ route('tickets.index', ['status' => 'open']) }}"
                                                    class="text-xs font-semibold text-accent hover:underline">
                                                    View all →
                                                </a>
                                            </div>

                                            <div id="bell-loading" class="hidden items-center justify-center py-8">
                                                <svg class="animate-spin h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                </svg>
                                            </div>

                                            <div id="bell-list" class="notif-dropdown divide-y divide-slate-100"></div>

                                            <div id="bell-empty" class="hidden flex-col items-center justify-center py-10 px-4 text-center">
                                                <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <p class="text-sm font-medium text-slate-500">All caught up!</p>
                                                <p class="text-xs text-slate-400 mt-1">No unassigned open tickets.</p>
                                            </div>

                                            <div class="px-4 py-3 border-t border-slate-100 bg-slate-50">
                                                <a href="{{ route('tickets.create') }}"
                                                    class="flex items-center justify-center gap-2 w-full py-2 btn-accent text-xs font-semibold rounded-lg transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                    New Ticket
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- User Dropdown -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" type="button"
                                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-slate-100 transition-colors text-sm">
                                        <div class="h-8 w-8 rounded-full btn-accent flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                            {{ substr(Auth::user()->name, 0, 2) }}
                                        </div>
                                        <div class="hidden sm:block text-left">
                                            <p class="text-sm font-medium text-slate-700 leading-tight">{{ Auth::user()->name }}</p>
                                            <p class="text-xs text-slate-400 leading-tight">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</p>
                                        </div>
                                        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div x-show="open" @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute right-0 z-10 mt-2 w-52 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-slate-200 overflow-hidden"
                                         style="display: none;">
                                        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                                            <p class="text-xs font-medium text-slate-500">Signed in as</p>
                                            <p class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->email }}</p>
                                        </div>
                                        <a href="{{ route('profile.edit') }}"
                                            class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            My Profile
                                        </a>
                                        
                                        <div class="border-t border-slate-100">
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit"
                                                    class="flex items-center gap-2 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                                    </svg>
                                                    Sign Out
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="px-4 sm:px-6 lg:px-8 pt-4 flash-message">
                        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="px-4 sm:px-6 lg:px-8 pt-4 flash-message">
                        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-sm">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <main class="flex-1">{{ $slot }}</main>

                <footer class="border-t border-slate-200 bg-white px-4 sm:px-6 lg:px-8 py-4">
                    <p class="text-xs text-slate-400 text-center">
                        TicketEase &copy; {{ date('Y') }} — Helpdesk & Ticketing System
                    </p>
                </footer>
            </div>
        </div>

        <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm hidden lg:hidden"></div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Sidebar mobile toggle
            const btn      = document.getElementById('mobile-menu-button');
            const sidebar  = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const closeBtn = document.getElementById('sidebar-close');

            function openSidebar()  { sidebar?.classList.remove('-translate-x-full'); backdrop?.classList.remove('hidden'); }
            function closeSidebar() { sidebar?.classList.add('-translate-x-full');    backdrop?.classList.add('hidden'); }

            btn?.addEventListener('click', openSidebar);
            closeBtn?.addEventListener('click', closeSidebar);
            backdrop?.addEventListener('click', closeSidebar);

            // Bell Notification
            const bellBtn      = document.getElementById('bell-btn');
            const bellDropdown = document.getElementById('bell-dropdown');
            const bellBadge    = document.getElementById('bell-badge');
            const bellList     = document.getElementById('bell-list');
            const bellLoading  = document.getElementById('bell-loading');
            const bellEmpty    = document.getElementById('bell-empty');

            if (!bellBtn) return;

            const priorityColors = {
                critical : 'bg-red-100 text-red-700',
                high     : 'bg-orange-100 text-orange-700',
                medium   : 'bg-blue-100 text-blue-700',
                low      : 'bg-slate-100 text-slate-600',
            };

            function loadCount() {
                fetch('{{ route('notifications.count') }}', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.count > 0) {
                        bellBadge.textContent = data.count > 9 ? '9+' : data.count;
                        bellBadge.classList.remove('hidden');
                        bellBadge.classList.add('flex');
                    } else {
                        bellBadge.classList.add('hidden');
                        bellBadge.classList.remove('flex');
                    }
                })
                .catch(() => {});
            }

            loadCount();
            setInterval(loadCount, 60000);

            bellBtn.addEventListener('click', function (e) {
                e.stopPropagation();

                const isOpen = !bellDropdown.classList.contains('hidden');
                if (isOpen) { bellDropdown.classList.add('hidden'); return; }

                bellDropdown.classList.remove('hidden');
                bellList.innerHTML = '';
                bellEmpty.classList.add('hidden');
                bellLoading.classList.remove('hidden');
                bellLoading.classList.add('flex');

                fetch('{{ route('notifications.index') }}', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    bellLoading.classList.add('hidden');
                    bellLoading.classList.remove('flex');
                    bellBadge.classList.add('hidden');
                    bellBadge.classList.remove('flex');

                    if (data.tickets.length === 0) {
                        bellEmpty.classList.remove('hidden');
                        bellEmpty.classList.add('flex');
                        return;
                    }

                    data.tickets.forEach(ticket => {
                        const item = document.createElement('a');
                        item.href  = ticket.url;
                        item.className = 'block px-4 py-3 hover:bg-slate-50 transition-colors';
                        item.innerHTML = `
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5 mb-0.5">
                                        <span class="text-xs font-mono text-slate-400">#${ticket.id}</span>
                                        <span class="px-1.5 py-0.5 text-[10px] font-semibold rounded-full ${priorityColors[ticket.priority] || priorityColors.low}">
                                            ${ticket.priority.charAt(0).toUpperCase() + ticket.priority.slice(1)}
                                        </span>
                                    </div>
                                    <p class="text-sm font-medium text-slate-800 truncate">${ticket.subject}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">${ticket.requester} · ${ticket.category}</p>
                                </div>
                                <span class="text-xs text-slate-400 whitespace-nowrap flex-shrink-0 mt-0.5">${ticket.created_at}</span>
                            </div>
                        `;
                        bellList.appendChild(item);
                    });
                })
                .catch(() => {
                    bellLoading.classList.add('hidden');
                    bellLoading.classList.remove('flex');
                    bellList.innerHTML = '<p class="text-xs text-center text-slate-400 py-6">Failed to load notifications.</p>';
                });
            });

            document.addEventListener('click', function (e) {
                if (!document.getElementById('notification-wrapper')?.contains(e.target)) {
                    bellDropdown?.classList.add('hidden');
                }
            });
        });
        </script>
    </body>
</html>