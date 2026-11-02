<x-app-layout>
<div class="py-12">
    <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Main Card Container -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6 transition-colors duration-200 border border-gray-200 dark:border-gray-700">
            
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-4">Edit Warehouse: {{ $warehouse->name }}</h1>
            
            @if ($errors->any())
                <div class="bg-red-100 dark:bg-red-900/40 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg text-sm">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('warehouses.update', $warehouse->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Warehouse Name</label>
                    <input type="text" name="name" value="{{ old('name', $warehouse->name)}}" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
                </div>
                
                <div class="flex items-center gap-2 pt-2">
                    <!-- Update Warehouse Button (Standard Indigo Tone) -->
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                        Update Warehouse
                    </button>
                    <!-- Cancel Button -->
                    <a href="{{ route('warehouses.index') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-5 py-2.5 rounded-lg text-sm font-semibold transition inline-flex items-center shadow-sm">
                        Cancel
                    </a>
                </div>
            </form>

        </div>

    </div>
</div>
</x-app-layout>