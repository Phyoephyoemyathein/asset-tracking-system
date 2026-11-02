<x-app-layout>
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Back Button Header -->
            <div class="flex justify-between items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition shadow-sm">
                    &larr; Back to Dashboard
                </a>
            </div>

            <!-- Activity Logs Table Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200 dark:border-gray-700 space-y-4">
                
                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Audit Trail & System Activity Logs</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Monitor system activities and user actions securely.</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse border border-gray-200 dark:border-gray-700 text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/80 border-b border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                                <th class="p-3 border border-gray-200 dark:border-gray-700">Date & Time</th>
                                <th class="p-3 border border-gray-200 dark:border-gray-700">User</th>
                                <th class="p-3 border border-gray-200 dark:border-gray-700">Action</th>
                                <th class="p-3 border border-gray-200 dark:border-gray-700">Subject</th>
                                <th class="p-3 border border-gray-200 dark:border-gray-700">Description</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                    <td class="p-3 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 text-xs">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-700 font-semibold text-gray-800 dark:text-gray-100">{{ $log->user->name ?? 'Unknown' }}</td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-700">
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded inline-block
                                            @if(strtolower($log->action) === 'created') bg-green-100 dark:bg-green-900/60 text-green-800 dark:text-green-300
                                            @elseif(strtolower($log->action) === 'updated') bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300
                                            @elseif(strtolower($log->action) === 'deleted') bg-red-100 dark:bg-red-900/60 text-red-800 dark:text-red-300
                                            @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @endif">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-700 font-medium text-gray-700 dark:text-gray-300">{{ $log->subject }}</td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 text-xs">{{ $log->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 border border-gray-200 dark:border-gray-700 text-center text-gray-400 dark:text-gray-500">No activity logs recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Optional Pagination Links if applicable -->
                @if(method_exists($logs, 'links'))
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>