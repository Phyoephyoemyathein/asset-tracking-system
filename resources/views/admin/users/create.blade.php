<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700 transition-colors duration-200">
                
                <!-- Card Header Banner -->
                <div class="bg-indigo-600 dark:bg-indigo-900 px-6 py-4 text-white">
                    <h3 class="text-lg font-bold">👤 Staff Account Creation</h3>
                    <p class="text-indigo-100 dark:text-indigo-200 text-xs">Fill in the details below to grant system access to a new staff member or administrator.</p>
                </div>

                <div class="p-8">
                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="mb-6 bg-green-50 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 rounded-r shadow-sm">
                            <p class="font-medium">Success!</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Aung Aung"
                                    class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm py-2.5">
                                @error('name') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Email Address -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                                <input type="email" name="email" value="{{ old('email') }}" required placeholder="staff@company.com"
                                    class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm py-2.5">
                                @error('email') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Role Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assign System Role</label>
                                <select name="role" required
                                    class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm py-2.5">
                                    <option value="staff">Staff (Regular User)</option>
                                    <option value="admin">Admin (Full Access)</option>
                                </select>
                                @error('role') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                                <input type="password" name="password" required placeholder="••••••••"
                                    class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm py-2.5">
                                @error('password') <span class="text-red-500 dark:text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                                <input type="password" name="password_confirmation" required placeholder="••••••••"
                                    class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm py-2.5">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition">
                                Cancel
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-semibold text-sm shadow-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>