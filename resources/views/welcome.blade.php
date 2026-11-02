<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Asset & Warehouse Tracking System</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Script to prevent theme flickering on load -->
        <script>
            if (localStorage.getItem('theme') === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        </script>
    </head>
    <body class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 antialiased font-sans transition-colors duration-200">
        <div class="min-h-screen flex flex-col justify-between">
            
            <!-- Navigation Bar -->
            <nav class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">📦 Asset & Warehouse Tracking System</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            
                            <!-- 💡 Real Dark/Light Mode Toggle Button -->
                            <button onclick="toggleTheme()" class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition focus:outline-none" title="Toggle Theme">
                                <span id="theme-icon">🌙</span>
                            </button>

                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 px-3 py-2 rounded-md">Log in</a>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl w-full text-center py-12">
                    <div class="inline-flex items-center justify-center p-3 bg-indigo-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full mb-6 shadow-md">
                        <span class="text-4xl">🏢</span>
                    </div>
                    <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-5xl mb-4">
                        Asset & Warehouse Management System
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-2xl mx-auto">
                        Efficiently manage company assets, inventory warehouses, item categories, and track stock in/out operations securely and seamlessly.
                    </p>
                    <div class="flex justify-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition">
                                Log in
                            </a>
                        @endauth
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-gray-100 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                Asset & Warehouse Tracking System &copy; 2026
            </footer>
        </div>

        <!-- Theme Toggle Script -->
        <script>
            const icon = document.getElementById('theme-icon');
            
            // Set initial icon based on current html class
            if (document.documentElement.classList.contains('dark')) {
                icon.textContent = '🌙';
            } else {
                icon.textContent = '☀️';
            }

            function toggleTheme() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                    icon.textContent = '☀️';
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                    icon.textContent = '🌙';
                }
            }
        </script>
    </body>
</html>