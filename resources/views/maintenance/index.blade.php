<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-900/40 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Back Button -->
            <div class="flex justify-between items-center">
                <a href="{{ route('assets.show', $asset->asset_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition shadow-sm">
                    &larr; Back to List
                </a>
            </div>

            <!-- Add Maintenance Form Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Maintenance History: {{ $asset->asset->name ?? '' }} (SKU: {{ $asset->serial_number }})
                </h2>
                
                <form action="{{ route('assets.maintenances.store', $asset->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Issue Description</label>
                            <input type="text" name="issue_description" required class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2.5">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Action Taken</label>
                            <input type="text" name="action_text" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2.5">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cost ($)</label>
                            <input type="number" step="0.01" name="cost" value="0" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2.5">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2.5">
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reported Date</label>
                            <input type="date" name="reported_date" required value="{{ date('Y-m-d') }}" class="w-full md:w-1/2 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2.5">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                            Save Record
                        </button>
                    </div>
                </form>
            </div>

            <!-- Maintenance History List Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Maintenance History List</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/80">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Issue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action Taken</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cost</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @forelse($asset->maintenances as $maintenance)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $maintenance->issue_description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $maintenance->action_taken ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">${{ number_format($maintenance->cost, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <form action="{{ route('assets.maintenances.update', $maintenance->id) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" onchange="this.form.submit()" class="text-xs rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 p-1.5
                                                @if($maintenance->status == 'completed') text-green-700 bg-green-50 dark:bg-green-900/20 
                                                @elseif($maintenance->status == 'in_progress') text-yellow-700 bg-yellow-50 dark:bg-yellow-900/20 
                                                @elseif($maintenance->status == 'failed') text-red-700 bg-red-50 dark:bg-red-900/20 
                                                @else text-gray-700 bg-gray-50 dark:bg-gray-900/20 @endif">
                                                <option value="pending" {{ $maintenance->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_progress" {{ $maintenance->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed" {{ $maintenance->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="failed" {{ $maintenance->status == 'failed' ? 'selected' : '' }}>Failed</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $maintenance->reported_date }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="{{ route('assets.maintenances.destroy', $maintenance->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition shadow-sm">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No maintenance records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>