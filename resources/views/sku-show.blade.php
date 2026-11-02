<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Header & Back Button -->
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    Asset Details for SKU: <span class="text-indigo-600 dark:text-indigo-400">{{ $sku }}</span>
                </h2>
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition text-sm font-semibold shadow-sm inline-block">
                    &larr; Back to Dashboard
                </a>
            </div>

            <!-- Warehouse အလိုက် စာရင်းဇယား -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-200 dark:border-gray-700 transition-colors duration-200 space-y-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Found in the following warehouses:</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse border border-gray-200 dark:border-gray-700 text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/80 border-b border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                                <th class="p-3 border border-gray-200 dark:border-gray-700">Asset Name</th>
                                <th class="p-3 border border-gray-200 dark:border-gray-700">Category</th>
                                <th class="p-3 border border-gray-200 dark:border-gray-700">Warehouse Location</th>
                                <th class="p-3 border border-gray-200 dark:border-gray-700 text-center">Quantity</th>
                                <th class="p-3 border border-gray-200 dark:border-gray-700 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800">
                            @foreach($assets as $asset)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                <td class="p-3 border border-gray-200 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-100">{{ $asset->name }}</td>
                                <td class="p-3 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">{{ $asset->category->name ?? '-' }}</td>
                                <td class="p-3 border border-gray-200 dark:border-gray-700">
                                    <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300 rounded text-xs font-semibold inline-block">
                                        {{ $asset->warehouse->name ?? '-' }}
                                    </span>
                                </td>
                                <!-- 💡 total_quantity ကို အသုံးပြု၍ Quantity အမှန်ကို ပြသခြင်း -->
                                <td class="p-3 border border-gray-200 dark:border-gray-700 text-center font-bold {{ $asset->total_quantity <= 5 ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-gray-200' }}">
                                    {{ $asset->total_quantity }}
                                </td>
                                <td class="p-3 border border-gray-200 dark:border-gray-700 text-center">
                                    <a href="{{ route('assets.show', $asset->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition inline-block shadow-sm">
                                        View Detail / Stock In-Out
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>