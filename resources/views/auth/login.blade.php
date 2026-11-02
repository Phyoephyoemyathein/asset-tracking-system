<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in - Asset & Warehouse Tracking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <!-- Script to prevent theme flickering on load -->
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 flex flex-col min-h-screen transition-colors duration-200">
    
    <!-- Navigation Bar -->
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-2">
                    <span class="text-2xl">📦</span>
                    <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">Asset & Warehouse Tracking System</span>
                </div>
                <div>
                    <button onclick="toggleTheme()" class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition focus:outline-none shadow-sm" title="Toggle Theme">
                        <span id="theme-icon">🌙</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 pb-12">
        <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 border border-gray-200 dark:border-gray-700">

            <!-- Logo / Title -->
            <div class="text-center mb-6">
                <span class="text-4xl">📦</span>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mt-2">Welcome Back</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Log in to Asset & Warehouse Tracking System</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 text-sm font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 p-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-500 text-white p-4 rounded mb-4 text-sm shadow">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-white p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('email')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Password</label>
                    <input type="password" name="password" required autocomplete="current-password"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-white p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('password')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-gray-600 dark:text-gray-300">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Forgot password?</a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-indigo-600 text-white p-2.5 rounded-lg font-semibold hover:bg-indigo-700 transition shadow-md">
                    Log in
                </button>
            </form>

            <!-- Register Link -->
            <div class="text-center mt-6 text-sm text-gray-600 dark:text-gray-400">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">Register</a>
            </div>

            <!-- Back / Home Link -->
            <div class="text-center mt-4">
                <a href="{{ url('/') }}" class="text-indigo-600 dark:text-indigo-400 font-semibold text-sm hover:underline inline-flex items-center gap-1">
                    &larr; Back to Welcome Page
                </a>
            </div>

        </div>
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