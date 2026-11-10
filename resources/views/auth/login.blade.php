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
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 flex flex-col min-h-screen transition-colors duration-200">
    
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
        <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">

            <!-- Logo / Title -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 text-2xl mb-2">
                    📦
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome Back</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Log in to Asset & Warehouse Tracking System</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 text-sm font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 p-3 rounded-lg border border-green-200 dark:border-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 p-3 rounded-lg border border-red-200 dark:border-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="w-full border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white px-3.5 py-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-gray-700 focus:outline-none transition">
                    @error('email')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password with Themed Eyelash Eye Icon Toggle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required autocomplete="current-password"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white px-3.5 py-2.5 pr-10 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-gray-700 focus:outline-none transition">
                        
                        <!-- Toggle Button with Theme-Matching Colors -->
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-indigo-500 dark:text-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-300 focus:outline-none transition">
                            <!-- မျက်လုံးပိတ်ပြီး မျက်တောင်မွှေးလေးတွေပါတဲ့ပုံ (Default) -->
                            <svg id="eye-closed" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10l2 2m16-2l-2 2M7 14l1.5 2m8-2l-1.5 2M12 15v3m-7-9a10 10 0 0114 0M3 12a10 10 0 0118 0" />
                            </svg>

                            <!-- မျက်လုံးဖွင့်ထားတဲ့ပုံ (Hidden by default) -->
                            <svg id="eye-open" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-sm py-1">
                    <label class="flex items-center text-gray-600 dark:text-gray-300 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                        <span class="ml-2">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Forgot password?</a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 px-4 rounded-lg font-semibold transition shadow-md shadow-indigo-600/20">
                    Log in
                </button>
            </form>

            <!-- Register Link -->
            <div class="text-center mt-6 text-sm text-gray-600 dark:text-gray-400">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">Register</a>
            </div>

            <!-- Back / Home Link -->
            <div class="text-center mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ url('/') }}" class="text-indigo-600 dark:text-indigo-400 font-medium text-sm hover:underline inline-flex items-center gap-1">
                    &larr; Back to Welcome Page
                </a>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script>
        const icon = document.getElementById('theme-icon');
        
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

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeClosed = document.getElementById('eye-closed');
            const eyeOpen = document.getElementById('eye-open');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeClosed.classList.add('hidden');
                eyeOpen.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>