<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-gray-900 dark:bg-gray-950 text-gray-100 mt-16 py-8 border-t border-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                        <div>
                            <h3 class="text-lg font-bold mb-4">{{ config('app.name', 'BookKeeping Platform') }}</h3>
                            <p class="text-gray-400">A complete equipment management system</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold mb-4">Quick Links</h3>
                            <ul class="space-y-2 text-gray-400">
                                <li><a href="{{ route('equipment.index') }}" class="hover:text-white transition">Equipment</a></li>
                                <li><a href="{{ route('users.index') }}" class="hover:text-white transition">Users</a></li>
                                <li><a href="{{ route('maintenanceRecord.index') }}" class="hover:text-white transition">Maintenance</a></li>
                                <li><a href="{{ route('equipmentHistory.index') }}" class="hover:text-white transition">History</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold mb-4">Support</h3>
                            <p class="text-gray-400">For support, contact admin@example.com</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                        <p>&copy; 2026 {{ config('app.name', 'BookKeeping Platform') }}. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>

