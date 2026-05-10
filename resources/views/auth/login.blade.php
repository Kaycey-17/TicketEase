<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TicketEase') }} — Login</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex">

        {{-- ═══════════════════════════════════════
             LEFT — Branding Panel
        ═══════════════════════════════════════ --}}
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden flex-col justify-between p-12"
            style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4338ca 100%);">

            {{-- Decorative blobs --}}
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full opacity-20"
                    style="background: radial-gradient(circle, #818cf8, transparent)"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 rounded-full opacity-10"
                    style="background: radial-gradient(circle, #c7d2fe, transparent)"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] rounded-full opacity-5"
                    style="background: radial-gradient(circle, #fff, transparent)"></div>
            </div>

            {{-- Top logo --}}
            <div class="relative z-10 flex items-center gap-3">
                <div class="bg-white/10 rounded-xl p-2 backdrop-blur-sm border border-white/10">
                    <img src="{{ asset('images/logo.png') }}" alt="TicketEase" class="h-9 w-auto">
                </div>
                <div>
                    <p class="text-white font-bold text-lg leading-tight">TicketEase</p>
                    <p class="text-indigo-300 text-xs">Helpdesk System</p>
                </div>
            </div>

            {{-- Center tagline --}}
            <div class="relative z-10">
                <h2 class="text-white text-4xl font-extrabold leading-snug mb-4">
                    Simple.<br>Fast.<br>Reliable.
                </h2>
                <p class="text-indigo-200 text-base max-w-xs leading-relaxed">
                    Manage support tickets and keep your team in sync — all in one place.
                </p>
            </div>

            {{-- Bottom stats --}}
            <div class="relative z-10 grid grid-cols-3 gap-3">
                @foreach([['500+', 'Users'], ['99.9%', 'Uptime'], ['24/7', 'Support']] as $stat)
                    <div class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-xl p-4 text-center">
                        <p class="text-white text-xl font-bold">{{ $stat[0] }}</p>
                        <p class="text-indigo-300 text-xs mt-0.5">{{ $stat[1] }}</p>
                    </div>
                @endforeach
            </div>

        </div>

        {{-- ═══════════════════════════════════════
             RIGHT — Login Form
        ═══════════════════════════════════════ --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 bg-slate-50">
            <div class="w-full max-w-sm">

                {{-- Mobile logo --}}
                <div class="lg:hidden flex items-center gap-3 mb-8">
                    <img src="{{ asset('images/logo.png') }}" alt="TicketEase" class="h-9 w-auto">
                    <div>
                        <p class="font-bold text-slate-800 text-lg leading-tight">TicketEase</p>
                        <p class="text-slate-400 text-xs">Helpdesk System</p>
                    </div>
                </div>

                {{-- Heading --}}
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-slate-900">Welcome back</h1>
                    <p class="text-slate-500 text-sm mt-1">Sign in to your account to continue</p>
                </div>

                {{-- Session status --}}
                @if(session('status'))
                    <div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Email Address
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="you@example.com"
                            class="w-full px-4 py-3 text-sm border rounded-lg transition
                                bg-white text-slate-900 placeholder-slate-400
                                focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                @error('email') border-red-400 bg-red-50 @else border-slate-300 @enderror">
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-medium text-slate-700">
                                Password
                            </label>
                            @if(Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-xs text-indigo-600 hover:text-indigo-700 font-medium transition-colors">
                                    Forgot password?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full px-4 py-3 pr-11 text-sm border rounded-lg transition
                                    bg-white text-slate-900 placeholder-slate-400
                                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                    @error('password') border-red-400 bg-red-50 @else border-slate-300 @enderror">
                            <button type="button" onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Remember me --}}
                    <div class="flex items-center">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 cursor-pointer">
                        <label for="remember_me" class="ml-2 text-sm text-slate-600 cursor-pointer select-none">
                            Keep me signed in
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full py-3 px-4 text-sm font-semibold text-white rounded-lg transition-all duration-200
                            bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] shadow-md shadow-indigo-500/20
                            focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Sign In
                    </button>

                </form>

                {{-- Footer --}}
                <p class="mt-8 text-center text-xs text-slate-400">
                    &copy; {{ date('Y') }} TicketEase. All rights reserved.
                </p>

            </div>
        </div>

    </div>

    <script>
        function togglePassword() {
            const input   = document.getElementById('password');
            const icon    = document.getElementById('eye-icon');
            const isHidden = input.type === 'password';

            input.type = isHidden ? 'text' : 'password';

            icon.innerHTML = isHidden
                ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`
                : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
        }
    </script>
</body>
</html>