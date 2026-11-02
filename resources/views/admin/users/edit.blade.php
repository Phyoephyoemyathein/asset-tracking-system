<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-200">

                <!-- Header with Title and Back Button on Same Line -->
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">
                    <h1 class="text-lg font-bold text-gray-800 dark:text-gray-100">Admin Panel — Edit system users and staff accounts</h1>
                    <a href="{{ route('admin.users.index') }}" class="bg-gray-600 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm whitespace-nowrap">
                        ← Back
                    </a>
                </div>

                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">Role</label>
                        <select name="role" class="w-full border border-gray-300 dark:border-gray-600 p-2.5 rounded-lg text-sm bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500" required>
                            <option value="staff" {{ strtolower(trim($user->role)) === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="admin" {{ strtolower(trim($user->role)) === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <a href="{{ route('admin.users.index') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg text-sm font-semibold transition">Cancel</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm">Update User</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>