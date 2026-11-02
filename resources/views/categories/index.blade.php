<x-app-layout>
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Main Card Container -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6 transition-colors duration-200">
            
           <!-- Top Header & Back Button -->
            <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <span>📂</span> Category Management
                </h3>
                <a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-lg text-sm font-semibold bg-gray-700 hover:bg-gray-800 dark:bg-gray-600 dark:hover:bg-gray-500 text-white transition inline-block">
                    Back to Dashboard
                </a>
            </div>

            <!-- Success & Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-900/50 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 dark:bg-red-900/50 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif
            
            <!-- Validation Errors Box -->
            @if ($errors->any())
                <div class="bg-red-100 dark:bg-red-900/50 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded no-print">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Add Category Form Section -->
            <div class="bg-white dark:bg-gray-700/50 p-6 rounded-lg shadow-md border border-gray-100 dark:border-gray-700 transition-colors duration-200">
                <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100">Add New Category</h3>
                <form action="{{ route('categories.store') }}" method="POST" class="flex gap-4">
                    @csrf
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Category Name (e.g. Electronics)" class="flex-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 p-2 rounded text-sm focus:ring-2 focus:ring-indigo-500" required>
                    <button type="submit" class="bg-purple-600 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-purple-700 transition">Save Category</button>
                </form>
            </div>

            <!-- Categories Table Section -->
            <div class="bg-white dark:bg-gray-700/50 p-6 rounded-lg shadow-md border border-gray-100 dark:border-gray-700 transition-colors duration-200">
                <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100">Category List</h3>
                <table class="w-full border-collapse border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-900/80 text-gray-700 dark:text-gray-300">
                            <th class="border border-gray-200 dark:border-gray-700 p-3 text-left">Name</th>
                            <th class="border border-gray-200 dark:border-gray-700 p-3 text-center">Associated Assets</th>
                            <th class="border border-gray-200 dark:border-gray-700 p-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                <td class="border border-gray-200 dark:border-gray-700 p-3 font-semibold text-gray-800 dark:text-gray-200">{{ $category->name }}</td>
                                <td class="border border-gray-200 dark:border-gray-700 p-3 text-center text-gray-800 dark:text-gray-200">{{ $category->assets_count }}</td>
                                <td class="border border-gray-200 dark:border-gray-700 p-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Edit Button -->
                                        <a href="{{ route('categories.edit', $category->id) }}" style="background-color: #f59e0b; color: #ffffff; padding: 6px 14px; border-radius: 6px; font-size: 13px; font-weight: bold; text-decoration: none; display: inline-block; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);">
                                            Edit
                                        </a>
                                        
                                        <!-- Delete Button -->
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background-color: #dc2626; color: #ffffff; padding: 6px 14px; border-radius: 6px; font-size: 13px; font-weight: bold; border: none; cursor: pointer; display: inline-block; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center p-4 text-gray-500 dark:text-gray-400">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
</x-app-layout>