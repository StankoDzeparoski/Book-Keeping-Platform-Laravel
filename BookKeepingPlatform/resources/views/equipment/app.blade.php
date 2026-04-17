<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Book Keeping Platform')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-8">
                    <a href="/" class="text-2xl font-bold text-blue-600">BookKeeping</a>
                    <div class="hidden md:flex gap-6">
                        <a href="{{ route('equipment.index') }}" class="text-gray-700 hover:text-blue-600 transition font-semibold">Equipment</a>
                        <a href="{{ route('equipmentHistory.index') }}" class="text-gray-700 hover:text-blue-600 transition font-semibold">History</a>
                        <a href="{{ route('maintenanceRecord.index') }}" class="text-gray-700 hover:text-blue-600 transition font-semibold">Maintenance</a>
                        <a href="{{ route('users.index') }}" class="text-gray-700 hover:text-blue-600 transition font-semibold">Users</a>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    @if(auth()->check())
                        <span class="text-gray-700">{{ auth()->user()->name }}</span>
                        <form action="#" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Logout</button>
                        </form>
                    @else
                        <a href="/login" class="text-blue-600 hover:text-blue-800 font-semibold">Login</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-900 text-gray-100 mt-16 py-8 border-t border-gray-800">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">BookKeeping Platform</h3>
                    <p class="text-gray-400">A complete equipment management system</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('equipment.index') }}" class="hover:text-white transition">Equipment</a></li>
                        <li><a href="{{ route('users.index') }}" class="hover:text-white transition">Users</a></li>
                        <li><a href="{{ route('maintenanceRecord.index') }}" class="hover:text-white transition">Maintenance</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Support</h3>
                    <p class="text-gray-400">For support, contact admin@example.com</p>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2026 BookKeeping Platform. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

