<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Detail - {{ $asset->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
   <style>
   /* 🖨️ Default Report Print Styles */
   @media print {
       body.print-report nav,
       body.print-report .no-print,
       body.print-report button,
       body.print-report .btn,
       body.print-report form,
       body.print-report .pagination,
       body.print-report a[href*="maintenances"],
       body.print-report td:last-child,
       body.print-report th:last-child,
       body.print-report td span.text-blue-500,
       body.print-report td span.text-blue-400,
       body.print-report #qr-sticker-section {
           display: none !important;
       }

       body.print-report #main-report-section,
       body.print-report #asset-summary-header {
           display: block !important;
       }

       body.print-report {
           background: white !important;
           color: black !important;
           padding: 0 !important;
           margin: 0 !important;
           width: 100% !important;
           font-size: 11px !important;
       }

       body.print-report .max-w-3xl {
           max-width: 100% !important;
           width: 100% !important;
           margin: 0 !important;
           padding: 0 !important;
       }

       body.print-report table {
           width: 100% !important;
           border-collapse: collapse !important;
           margin-top: 10px !important;
           border: 2px solid #000000 !important;
       }

       body.print-report th, body.print-report td {
           border: 1.5px solid #000000 !important;
           padding: 6px 8px !important;
           text-align: center !important; 
       }

       body.print-report th {
           background-color: transparent !important;
           color: #000000 !important;
           font-weight: 900 !important;
           border-bottom: 2.5px solid #000000 !important;
       }

       body.print-report td svg, body.print-report td img {
           width: 55px !important;
           height: 55px !important;
           margin: 0 auto;
       }

       body.print-report tr {
           page-break-inside: avoid;
       }
       
       body.print-report thead {
           display: table-header-group;
       }
   }

   /* 🏷️ QR Stickers Sheet Print Styles (Grid Layout for manual cutting) */
   @media print {
       body.print-stickers nav,
       body.print-stickers .no-print,
       body.print-stickers button,
       body.print-stickers .btn,
       body.print-stickers form,
       body.print-stickers .pagination,
       body.print-stickers #main-report-section,
       body.print-stickers #asset-summary-header {
           display: none !important;
       }

       body.print-stickers {
           background: white !important;
           color: black !important;
           padding: 10px !important;
           margin: 0 !important;
           width: 100% !important;
       }

       body.print-stickers #qr-sticker-section {
           display: grid !important;
           grid-template-columns: repeat(4, 1fr) !important;
           gap: 12px !important;
           width: 100% !important;
       }

       .sticker-card {
           border: 1.5px dashed #64748b !important;
           padding: 10px !important;
           text-align: center !important;
           border-radius: 6px !important;
           background: white !important;
           page-break-inside: avoid;
       }

       .sticker-card svg, .sticker-card img {
           width: 75px !important;
           height: 75px !important;
           margin: 0 auto !important;
       }
   }
</style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 min-h-screen transition-colors duration-200" 
      x-data="{ activeModalId: null, activeQrCode: '', activeSerialNo: '', activeTab: 'movement' }"
      id="pageBody">

    <!-- 🌐 Top Navigation Bar -->
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm no-print mb-8">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center shrink-0">
                    <a href="{{ route('dashboard') }}" class="text-base sm:text-xl font-bold text-gray-800 dark:text-gray-100 truncate">📦 Asset & Warehouse Tracking System</a>
                </div>

                <div class="flex items-center gap-2">
                    <button onclick="toggleDarkMode()" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-700 dark:text-gray-200 transition focus:outline-none flex items-center gap-1">
                        <span id="themeIcon">🌙</span>
                    </button>
                    <a href="{{ route('dashboard') }}" class="bg-slate-700 hover:bg-slate-800 text-white px-3 py-1.5 rounded text-xs font-semibold transition shadow-sm">Dashboard</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto space-y-6 px-4 pb-12">
        
        <!-- Navigation & Dual Print Buttons -->
        <div class="flex flex-wrap justify-between items-center gap-2 no-print">
            <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition text-sm font-semibold shadow-sm">← Back</a>

            <div class="flex items-center gap-2">
                <!-- 🖨️ Print Report Button (Primary Action) -->
                <button onclick="printReport()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-semibold transition flex items-center gap-1 shadow-sm">
                    🖨️ Print Report
                </button>

                <!-- 🏷️ Print QR Stickers Button (Secondary Action) -->
                <button onclick="printStickers()" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded text-sm font-semibold transition flex items-center gap-1 shadow-sm">
                    🏷️ Print QR Stickers
                </button>
            </div>
        </div>

        <!-- Main Asset Information & Summary Cards -->
        <div id="asset-summary-header" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
            <div class="flex justify-between items-start border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $asset->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">SKU: {{ $asset->sku }}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-400 block">Available Total Quantity</span>
                    <span class="text-xl font-bold text-gray-800 dark:text-gray-100">
                        {{ $asset->items()->where('status', 'available')->count() }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <span class="block text-xs font-medium text-gray-400">Category</span>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $asset->category->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-400">Warehouse Location</span>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $asset->warehouse->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-400">Registered Date</span>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $asset->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-gray-400">Serial Items Count</span>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $asset->items->count() }} Items</span>
                </div>
            </div>
        </div>

        <!-- 💡 Individual Items & QR Codes (Table View for Report) -->
        <div id="main-report-section" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md space-y-4 border border-gray-100 dark:border-gray-700 transition-colors duration-200">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">Individual Items & QR Codes (Serial Level)</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse border border-gray-200 dark:border-gray-700 text-sm">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-900/80 border-b border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                            <th class="p-3 border border-gray-200 dark:border-gray-700">Serial Number</th>
                            <th class="p-3 border border-gray-200 dark:border-gray-700">Item QR Code</th>
                            <th class="p-3 border border-gray-200 dark:border-gray-700">Status</th>
                            <th class="p-3 border border-gray-200 dark:border-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                        @forelse($asset->items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition {{ $item->status == 'out' ? 'opacity-60 bg-gray-50 dark:bg-gray-900/40' : '' }}">
                            <td class="p-3 border border-gray-200 dark:border-gray-700 font-mono text-cyan-600 dark:text-cyan-400 font-semibold">{{ $item->serial_number }}</td>
                            
                            <td class="p-3 border border-gray-200 dark:border-gray-700 cursor-pointer group text-center"
                                @click="activeModalId = 'modal-{{ $item->id }}'">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 [&>svg]:w-full [&>svg]:h-full bg-white p-1 rounded shadow-sm group-hover:ring-2 group-hover:ring-indigo-500 transition">
                                        {!! $item->qr_code !!}
                                    </div>
                                    <span class="text-[10px] text-blue-500 dark:text-blue-400 group-hover:underline block mt-1">🔍 Click to view</span>
                                </div>
                            </td>
                            
                            <td class="p-3 border border-gray-200 dark:border-gray-700">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded 
                                    @if($item->status == 'available') bg-green-100 dark:bg-green-900/60 text-green-800 dark:text-green-300
                                    @elseif($item->status == 'maintenance') bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300
                                    @elseif($item->status == 'failed') bg-red-100 dark:bg-red-900/60 text-red-800 dark:text-red-300
                                    @else bg-gray-100 dark:bg-gray-900/60 text-gray-800 dark:text-gray-300 @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>

                            <td class="p-3 border border-gray-200 dark:border-gray-700">
                                @if($item->status == 'available' || $item->status == 'maintenance')
                                    <!-- Maintenance (Secondary Slate / Gray) -->
                                    <a href="{{ route('assets.maintenances.index', $item->id) }}" class="inline-block bg-slate-700 hover:bg-slate-800 text-white font-semibold py-1.5 px-3 rounded text-xs transition shadow-sm">
                                        🛠️ Maintenance
                                    </a>
                                @elseif($item->status == 'failed')
                                    <!-- Retry Maintenance (Warning Amber Tone) -->
                                    <a href="{{ route('assets.maintenances.index', $item->id) }}" class="inline-block bg-amber-500 hover:bg-amber-600 text-white font-semibold py-1.5 px-3 rounded text-xs transition shadow-sm">
                                        🛠️ Retry Maintenance
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500 italic">Checked Out</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-6 border border-gray-200 dark:border-gray-700 text-center text-gray-400 dark:text-gray-500">No individual items registered for this asset.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
            
        </div>

        <!-- 🏷️ QR Code Stickers Grid Section -->
        <div id="qr-sticker-section" style="display: none;">
            @forelse($asset->items->where('status', 'available') as $item)
                <div class="sticker-card">
                    <p class="text-[10px] font-bold text-gray-600 truncate">{{ $asset->name }}</p>
                    <div class="my-1 flex justify-center">
                        {!! $item->qr_code !!}
                    </div>
                    <p class="text-[11px] font-mono font-bold text-black">{{ $item->serial_number }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500 col-span-full text-center">No available items to print stickers.</p>
            @endforelse
        </div>

        <!-- Modals -->
        @foreach($asset->items as $item)
        <div x-show="activeModalId === 'modal-{{ $item->id }}'" 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm" 
             style="display: none;" 
             @click.away="activeModalId = null">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-2xl text-center max-w-xs w-full border border-gray-200 dark:border-gray-700" @click.stop>
                <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Serial QR Code</h3>
                <p class="text-base font-mono font-bold text-cyan-600 dark:text-cyan-400 mb-4">{{ $item->serial_number }}</p>
                
                <div class="bg-white p-4 rounded-lg shadow-inner border border-gray-100 flex justify-center items-center my-2">
                    <div class="w-48 h-48 [&>svg]:w-full [&>svg]:h-full">
                        {!! $item->qr_code !!}
                    </div>
                </div>
                
                <button @click="activeModalId = null" class="mt-5 w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                    Close
                </button>
            </div>
        </div>
        @endforeach

        <!-- 🗂️ Interactive Tabs Navigation (no-print) -->
        <div class="no-print border-b border-gray-200 dark:border-gray-700 mt-8">
            <nav class="flex space-x-2" aria-label="Tabs">
                <button @click="activeTab = 'movement'" 
                        :class="activeTab === 'movement' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-white dark:bg-gray-800 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition flex items-center gap-2">
                    🔄 Stock Movement
                </button>
                <button @click="activeTab = 'transfer'" 
                        :class="activeTab === 'transfer' ? 'border-amber-500 text-amber-600 dark:text-amber-400 bg-white dark:bg-gray-800 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition flex items-center gap-2">
                    🚚 Warehouse Transfer
                </button>
                <button @click="activeTab = 'history'" 
                        :class="activeTab === 'history' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-white dark:bg-gray-800 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition flex items-center gap-2">
                    📜 Transaction History
                </button>
            </nav>
        </div>

        <!-- 📂 Tab Contents Area -->
        <div class="no-print">
            <!-- 1. Stock In / Out Form Section -->
            <div x-show="activeTab === 'movement'" x-transition 
                 class="bg-white dark:bg-gray-800 p-6 rounded-b-lg rounded-tr-lg shadow-md space-y-4 border border-gray-100 dark:border-gray-700 transition-colors duration-200" 
                 x-data="{ stockType: 'in' }">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">Stock Movement (In / Out)</h2>

                @if(session('stock_success'))
                    <div class="bg-green-100 dark:bg-green-900/50 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded text-sm">
                        {{ session('stock_success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 dark:bg-red-900/50 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('stock.store', $asset->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Transaction Type</label>
                            <select name="type" x-model="stockType" required class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 rounded-md p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="in">Stock In</option>
                                <option value="out">Stock Out</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reference / Note</label>
                            <input type="text" name="reference" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 rounded-md p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Voucher or Reason">
                        </div>
                    </div>

                    <div x-show="stockType === 'in'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
                        <input type="number" name="quantity" min="1" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 rounded-md p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter qty">
                    </div>

                    <div x-show="stockType === 'out'" class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Serial Numbers to Output:</label>
                        <div class="max-h-48 overflow-y-auto border border-gray-200 dark:border-gray-700 p-3 rounded-md bg-gray-50 dark:bg-gray-900/50 space-y-2">
                            @forelse($asset->items->where('status', 'available') as $item)
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="checkbox" name="serial_ids[]" value="{{ $item->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="font-mono text-sm text-gray-700 dark:text-gray-300">{{ $item->serial_number }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-400">No available serial items to output.</p>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <!-- Submit Transaction (Primary Blue/Indigo Action) -->
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md text-sm transition shadow-sm">
                            Submit Transaction
                        </button>
                    </div>
                </form>
            </div>

            <!-- 2. 🚚 Asset Transfer Card -->
            <div x-show="activeTab === 'transfer'" x-transition 
                 class="bg-white dark:bg-gray-800 p-6 rounded-b-lg rounded-tr-lg shadow-md space-y-4 border border-gray-100 dark:border-gray-700 transition-colors duration-200"
                 style="display: none;">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">🚚 Transfer Asset to Another Warehouse</h2>

                @if(session('transfer_success'))
                    <div class="bg-green-100 dark:bg-green-900/50 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded text-sm">
                        {{ session('transfer_success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 dark:bg-red-900/50 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded text-sm">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('assets.transfer', $asset->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="current_warehouse_id" value="{{ $asset->warehouse_id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Destination Warehouse</label>
                            <select name="to_warehouse_id" required class="w-full border border-gray-300 dark:border-gray-600 rounded-md p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100">
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    @if($warehouse->id !== $asset->warehouse_id)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reference / Note</label>
                            <input type="text" name="reference" placeholder="e.g., Transfer to main branch" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 rounded-md p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Available Serials to Transfer:</label>
                        <div class="max-h-48 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-3 rounded-md border border-gray-300 dark:border-gray-700 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                            @forelse($asset->items()->where('status', 'available')->get() as $item)
                                <label class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 text-sm p-1 hover:bg-gray-100 dark:hover:bg-gray-800 rounded cursor-pointer">
                                    <input type="checkbox" name="serial_ids[]" value="{{ $item->id }}" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-800">
                                    <span class="font-mono">{{ $item->serial_number }}</span>
                                </label>
                            @empty
                                <span class="text-sm text-gray-500 dark:text-gray-400 col-span-full">No available serials found for transfer.</span>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <!-- Transfer Asset (Warning Amber Action) -->
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 px-6 rounded-md text-sm transition shadow-sm">
                            Transfer Asset
                        </button>
                    </div>
                </form>
            </div>

            <!-- 3. Transaction History Table -->
            <div x-show="activeTab === 'history'" x-transition 
                 class="bg-white dark:bg-gray-800 p-6 rounded-b-lg rounded-tr-lg shadow-md space-y-4 border border-gray-100 dark:border-gray-700 transition-colors duration-200"
                 style="display: none;">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">Stock Transaction History</h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse border border-gray-200 dark:border-gray-700 text-sm">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-900/80 border-b border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                                <th class="p-3 border border-gray-200 dark:border-gray-700">Date</th>
                                <th class="p-3 border border-gray-200 dark:border-gray-700">Type</th>
                                <th class="p-3 border border-gray-200 dark:border-gray-700">Quantity</th>
                                <th class="p-3 border border-gray-200 dark:border-gray-700">Reference / Note</th>
                                <th class="p-3 border border-gray-200 dark:border-gray-700">Performed By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                            @forelse($transactions as $tx)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                    <td class="p-3 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-700">
                                        @if($tx->type === 'in')
                                            <span class="bg-green-100 dark:bg-green-900/60 text-green-800 dark:text-green-300 text-xs font-semibold px-2.5 py-1 rounded">Stock In</span>
                                        @elseif($tx->type === 'out')
                                            <span class="bg-red-100 dark:bg-red-900/60 text-red-800 dark:text-red-300 text-xs font-semibold px-2.5 py-1 rounded">Stock Out</span>
                                        @else
                                            <span class="bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 text-xs font-semibold px-2.5 py-1 rounded">Transfer</span>
                                        @endif
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-700 font-semibold {{ $tx->type === 'in' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $tx->type === 'in' ? '+' : '-' }}{{ $tx->quantity }}
                                    </td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">{{ $tx->reference ?? '-' }}</td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">{{ $tx->user->name ?? 'System' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 border border-gray-200 dark:border-gray-700 text-center text-gray-400 dark:text-gray-500">No transactions recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script>
        // 🖨️ Print Report Function
        function printReport() {
            const body = document.getElementById('pageBody');
            body.classList.remove('print-stickers');
            body.classList.add('print-report');
            window.print();
        }

        // 🏷️ Print QR Stickers Function
        function printStickers() {
            const body = document.getElementById('pageBody');
            body.classList.remove('print-report');
            body.classList.add('print-stickers');
            window.print();
        }

        // 🌙 Dark Mode Toggle Function
        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                updateThemeIcon('light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                updateThemeIcon('dark');
            }
        }

        function updateThemeIcon(theme) {
            const icon = document.getElementById('themeIcon');
            if (icon) {
                icon.textContent = theme === 'dark' ? '☀️' : '🌙';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const isDark = document.documentElement.classList.contains('dark');
            updateThemeIcon(isDark ? 'dark' : 'light');
        });
    </script>
</body>
</html>