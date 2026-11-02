<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-200">
                
                <!-- Success / Error Messages -->
                @if(session('success'))
                    <div class="mb-4 bg-green-50 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 rounded-r shadow-sm text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 bg-red-50 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 rounded-r shadow-sm text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="flex justify-between items-center bg-white dark:bg-gray-700/50 p-4 rounded-lg shadow-md mb-6 border border-gray-100 dark:border-gray-700 transition-colors duration-200">
                    <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Admin Panel — Manage system users and staff accounts</h1>
                    <a href="{{ url('/dashboard') }}" class="bg-gray-600 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 text-white px-4 py-2 rounded text-sm font-semibold transition shadow-sm">Back to Dashboard</a>
                </div>

                <!-- Users Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse border border-gray-200 dark:border-gray-700">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/80 border-b border-gray-200 dark:border-gray-700 text-xs uppercase text-gray-600 dark:text-gray-300 tracking-wider">
                                <th class="py-3 px-4 border border-gray-200 dark:border-gray-700">Name</th>
                                <th class="py-3 px-4 border border-gray-200 dark:border-gray-700">Email</th>
                                <th class="py-3 px-4 border border-gray-200 dark:border-gray-700">Role</th>
                                <th class="py-3 px-4 border border-gray-200 dark:border-gray-700">Created At</th>
                                <th class="py-3 px-4 border border-gray-200 dark:border-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm text-gray-800 dark:text-gray-200">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                    <td class="py-3 px-4 border border-gray-200 dark:border-gray-700 font-medium text-gray-800 dark:text-gray-100">{{ $user->name }}</td>
                                    <td class="py-3 px-4 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                                    <td class="py-3 px-4 border border-gray-200 dark:border-gray-700">
                                        @if(strtolower(trim($user->role)) === 'admin')
                                            <span class="bg-purple-100 dark:bg-purple-900/60 text-purple-700 dark:text-purple-300 text-xs px-2.5 py-1 rounded-full font-semibold">Admin</span>
                                        @else
                                            <span class="bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300 text-xs px-2.5 py-1 rounded-full font-semibold">Staff</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 text-xs">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="py-3 px-4 border border-gray-200 dark:border-gray-700" style="white-space: nowrap;">
                                        <!-- Edit Button -->
                                        <a href="{{ route('admin.users.edit', $user->id) }}" style="background-color: #f59e0b; color: #ffffff; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-block; margin-right: 4px;">
                                            Edit
                                        </a>

                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display: inline-block; margin: 0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background-color: #ef4444; color: #ffffff; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; display: inline-block;">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <span style="color: #9ca3af; font-size: 12px; font-style: italic; background-color: #374151; padding: 6px 10px; border-radius: 6px; display: inline-block;" class="dark:bg-gray-700 dark:text-gray-300">Current User</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 border border-gray-200 dark:border-gray-700 text-center text-gray-500 dark:text-gray-400">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4 text-gray-700 dark:text-gray-300">
                    {{ $users->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>