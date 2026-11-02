<x-app-layout>
    <div class="py-12">
        <!-- 💡 Width ကို Inline CSS ဖြင့် တိုက်ရိုက်ကန့်သတ်ခြင်း -->
        <div style="max-width: 550px; margin: 0 auto;" class="sm:px-6 lg:px-8">
            
            <!-- Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6 border border-gray-100 dark:border-gray-700 transition-colors duration-200">
                
                <!-- Logo / Title -->
                <div class="text-center border-b border-gray-200 dark:border-gray-700 pb-4">
                    <span class="text-3xl">📦</span>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mt-1">Register New Account</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Register for Asset & Warehouse Tracking System</p>
                </div>

                <!-- Success Message Box -->
                @if (session('success'))
                    <div class="p-3 bg-green-100 dark:bg-green-900/50 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 rounded-lg text-sm font-medium text-center">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Validation Errors Box -->
                @if ($errors->any())
                    <div class="p-3 bg-red-100 dark:bg-red-900/50 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 rounded-lg text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Register Form -->
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('name')
                            <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('email')
                            <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Password</label>
                        <input type="password" name="password" required autocomplete="new-password"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('password')
                            <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('password_confirmation')
                            <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Role Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Role</label>
                        <select name="role" required class="w-full border border-gray-300 dark:border-gray-600 p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100">
                            <option value="" disabled selected>Select Role</option>
                            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white p-2.5 rounded-lg font-semibold transition shadow-md mt-2">
                        Register
                    </button>
                </form>

                <!-- Back & Login Links -->
                <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700 text-sm">
                    <a href="{{ url('/dashboard') }}" class="text-gray-600 dark:text-gray-400 font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition flex items-center gap-1">
                        &larr; Back
                    </a>
                    <span class="text-gray-500 dark:text-gray-400">
                        Already registered? <a href="{{ route('login') }}" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">Log in</a>
                    </span>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>