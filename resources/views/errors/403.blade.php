<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Unauthorized | TicketEase</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        <div class="max-w-md w-full text-center">

            <!-- Logo -->
            <div class="mb-6">
                <svg class="w-16 h-16 text-blue-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <h1 class="text-2xl font-bold text-gray-900 mt-2">TicketEase</h1>
            </div>

            <!-- Error Card -->
            <div class="bg-white rounded-lg shadow-md p-8">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m2-5V9m0 0V7m0 2h2m-2 0H10M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <h2 class="text-6xl font-bold text-gray-900 mb-2">403</h2>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Access Denied</h3>
                <p class="text-gray-500 mb-6">
                    You don't have permission to access this page.
                    Please contact your administrator if you think this is a mistake.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('dashboard') }}"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                        Go to Dashboard
                    </a>
                    <a href="javascript:history.back()"
                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md text-sm font-medium">
                        Go Back
                    </a>
                </div>
            </div>

            <p class="text-xs text-gray-400 mt-4">
                TicketEase Helpdesk System
            </p>
        </div>
    </div>
</body>
</html>