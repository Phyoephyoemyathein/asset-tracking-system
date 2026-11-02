<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Asset & Warehouse Tracking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        
        <!-- Logo / Title -->
        <div class="text-center mb-6">
            <span class="text-4xl">📦</span>
            <h2 class="text-2xl font-bold text-gray-800 mt-2">Forgot Password</h2>
            <p class="text-sm text-gray-500 mt-2">
                Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
            </p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded">
                {{ session('status') }}
            </div>
        @endif

        <!-- Forgot Password Form -->
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full border border-gray-300 p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('email')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-indigo-600 text-white p-2.5 rounded-lg font-semibold hover:bg-indigo-700 transition shadow-md">
                Email Password Reset Link
            </button>
        </form>

        <!-- Back to Login -->
        <div class="text-center mt-6 text-sm text-gray-600">
            <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:underline">← Back to Log in</a>
        </div>

    </div>
</body>
</html>
