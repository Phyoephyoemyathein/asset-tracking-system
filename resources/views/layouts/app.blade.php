<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js CDN -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- 🌙 Global Dark Mode Script & Function -->
        <script>
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            }

            function toggleDarkMode() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                    updateThemeIcon('light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                    updateThemeIcon('dark');
                }
            }

            function updateThemeIcon(theme) {
                const icon = document.getElementById('themeIcon');
                if (icon) {
                    icon.textContent = theme === 'dark' ? '☀️' : '🌙';
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                const isDark = document.documentElement.classList.contains('dark');
                updateThemeIcon(isDark ? 'dark' : 'light');
            });
        </script>
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 min-h-screen transition-colors duration-200" x-data="{ sidebarOpen: false }">
        
        <div class="min-h-screen flex flex-col">

            <!-- 🌐 Top Navigation Bar -->
            <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm no-print h-16 shrink-0 z-30 sticky top-0 flex items-center">
                <div class="w-full px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <!-- System Logo / Name -->
                    <div class="flex items-center gap-3 pl-0">
                        <!-- Mobile Hamburger Button -->
                        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-600 dark:text-gray-300 focus:outline-none p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <a href="{{ route('dashboard') }}" class="text-base sm:text-xl font-extrabold text-gray-800 dark:text-gray-100">📦 Asset & Warehouse Tracking System</a>
                    </div>

                    <!-- Right Top Menu Items -->
                    <div class="flex items-center gap-3">
                        <!-- 🌙 Theme Toggle -->
                        <button onclick="toggleDarkMode()" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 p-2 rounded-lg text-xs font-medium text-gray-700 dark:text-gray-200 transition focus:outline-none flex items-center gap-1">
                            <span id="themeIcon">🌙</span>
                        </button>

                        <!-- 👤 User Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-700 dark:text-gray-200 transition focus:outline-none">
                                <span class="font-bold text-gray-900 dark:text-gray-100 truncate">👤 {{ auth()->user()->name ?? 'User' }}</span>
                                <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-[10px] font-semibold px-1.5 py-0.5 rounded-full uppercase">
                                    {{ auth()->user()->role ?? 'Staff' }}
                                </span>
                                <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-1 z-50" style="display: none;">
                                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Signed in as</p>
                                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">{{ auth()->user()->email ?? '' }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium transition">⚙️ Profile Settings</a>
                                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-gray-700 font-semibold transition">🚪 Log Out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Body Layout (Sidebar + Content Container) -->
            <div class="flex-1 flex overflow-hidden relative">

                <!-- 📱 Mobile Overlay Backdrop -->
                <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-20 md:hidden" style="display: none;"></div>

                <!-- 📌 Sidebar -->
               <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:sticky top-0 inset-y-0 left-0 z-30 w-72 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col justify-between p-4 no-print shrink-0 overflow-y-auto transition-transform duration-300 md:translate-x-0 md:h-[calc(100vh-4rem)]">
                    <div class="space-y-6">
                        
                        <nav class="space-y-2">
                            <p class="px-3 pb-1 text-xs font-bold text-gray-400 uppercase tracking-wider">Navigation</p>
                            
                           
                            <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg {{ request()->routeIs('dashboard') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition text-left">
                                📦 Asset Inventory
                            </a>
                            <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition text-left">
                                📸 QR & Barcode Scanner
                            </a>
                            <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition text-left">
                                📊 Reports & Analytics
                            </a>

                            @if(auth()->check() && strtolower(trim(auth()->user()->role)) === 'admin')

                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-1.5">
                                <p class="px-3 pb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Management</p>
                                
                                <a href="{{ route('categories.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg {{ request()->routeIs('categories.*') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">📂 Categories</a>
                                <a href="{{ route('warehouses.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg {{ request()->routeIs('warehouses.*') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">🏢 Warehouses</a>
                                <a href="{{ route('admin.users.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg {{ request()->routeIs('admin.users.index') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">👥 Users</a>
                                <a href="{{ route('admin.users.create') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg {{ request()->routeIs('admin.users.create') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">➕ Add Staff</a>
                                <a href="{{ route('activity.logs') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg {{ request()->routeIs('activity.logs') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">📋 Activity Logs</a>
                            </div>
                            @endif
                        </nav>
                    </div>
                </aside>

                <!-- 🖥️ Main Workspace Content -->
                <main class="flex-1 overflow-y-auto h-[calc(100vh-4rem)]">
                    {{ $slot }}
                </main>

            </div>
        </div>
    </body>
</html>