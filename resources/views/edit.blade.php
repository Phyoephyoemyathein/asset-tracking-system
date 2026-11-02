<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            
            <!-- 📦 Card တစ်ခုတည်းအတွင်း အားလုံး စုစည်းထားခြင်း (Dark Mode ထည့်သွင်းထားသည်) -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6 border border-gray-200 dark:border-gray-700">

                <!-- Header & Back Button -->
                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">✏️ Edit Asset: {{ $asset->name }}</h3>
                    <a href="{{ url('/dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm">
                        ← Back
                    </a>
                </div>

                <!-- Success Message -->
                @if(session('success'))
                    <div class="bg-green-100 dark:bg-green-900/40 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="bg-red-100 dark:bg-red-900/40 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Edit Form -->
                <form action="{{ route('assets.update', $asset->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-1">Category</label>
                        <select name="category_id" class="w-full border border-gray-300 dark:border-gray-600 p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $asset->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-1">Warehouse</label>
                        <select name="warehouse_id" class="w-full border border-gray-300 dark:border-gray-600 p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100" required>
                            <option value="">Select Warehouse</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $asset->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-1">Asset Name</label>
                        <input type="text" name="name" value="{{ old('name', $asset->name) }}" class="w-full border border-gray-300 dark:border-gray-600 p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-1">SKU / Serial Number</label>
                        <input type="text" name="sku" value="{{ old('sku', $asset->sku) }}" class="w-full border border-gray-300 dark:border-gray-600 p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-1">Quantity</label>
                        <input type="number" name="quantity" value="{{ old('quantity', $asset->quantity) }}" class="w-full border border-gray-300 dark:border-gray-600 p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-1">Minimum Stock Level</label>
                        <input type="number" name="min_stock_level" value="{{ old('min_stock_level', $asset->min_stock_level ?? 5) }}" class="w-full border border-gray-300 dark:border-gray-600 p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100" required>
                    </div>

                    <div>
                        <!-- Update Asset Button (Standard Indigo Tone) -->
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white p-2.5 rounded-lg text-sm font-semibold transition shadow-md mt-2">
                            Update Asset
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </div>
</x-app-layout>