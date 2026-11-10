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
       body.print-report aside,
       body.print-report .no-print,
       body.print-report button,
       body.print-report .btn,
       body.print-report form,
       body.print-report .pagination,
       body.print-report a[href*="maintenances"],
       /* 💡 Screen ဇယား၏ နောက်ဆုံးကော်လံ (Actions) ကိုသာ ဖုံးမည်၊ print-table ကို ချန်လှပ်မည် */
       body.print-report .screen-table td:last-child,
       body.print-report .screen-table th:last-child,
       body.print-report td span.text-blue-500,
       body.print-report td span.text-blue-400,
       body.print-report #qr-sticker-section,
       body.print-report .screen-table,
       body.print-report .screen-pagination {
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

       body.print-report .max-w-5xl {
           max-width: 100% !important;
           width: 100% !important;
           margin: 0 !important;
           padding: 0 !important;
       }

       /* Print Table ($allItems အားလုံးပါသော) ကို ပုံနှိပ်ချိန်တွင် ပေါ်စေရန် */
       body.print-report .print-table {
           display: table !important;
           width: 100% !important;
           border-collapse: collapse !important;
           margin-top: 10px !important;
           border: 2px solid #000000 !important;
       }

       body.print-report .print-table th, 
       body.print-report .print-table td {
           border: 1.5px solid #000000 !important;
           padding: 6px 8px !important;
           text-align: center !important; 
       }

       body.print-report .print-table th {
           background-color: transparent !important;
           color: #000000 !important;
           font-weight: 900 !important;
           border-bottom: 2.5px solid #000000 !important;
       }

       body.print-report .print-table td svg, 
       body.print-report .print-table td img {
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
       body.print-stickers aside,
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
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm no-print h-16 shrink-0 z-20 sticky top-0 flex items-center">
        <div class="w-full px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <!-- System Logo / Name -->
            <div class="flex items-center gap-3 pl-0">
                <a href="{{ route('dashboard') }}" class="text-base sm:text-xl font-extrabold text-gray-800 dark:text-gray-100">📦 Asset & Warehouse Tracking System</a>
            </div>

            <!-- Right Top Menu Items -->
            <div class="flex items-center gap-3">
                <!-- 🌙 Theme Toggle -->
                <button onclick="toggleDarkMode()" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 p-2 rounded-lg text-xs font-medium text-gray-700 dark:text-gray-200 transition focus:outline-none flex items-center gap-1">
                    <span id="themeIcon">🌙</span>
                </button>

                <!-- 👤 User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-700 dark:text-gray-200 transition focus:outline-none">
                        <span class="font-bold text-gray-900 dark:text-gray-100 truncate">👤 {{ auth()->user()->name ?? 'User' }}</span>
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-[10px] font-semibold px-1.5 py-0.5 rounded-full uppercase">
                            {{ auth()->user()->role ?? 'Staff' }}
                        </span>
                        <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-1 z-50" style="display: none;">
                        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Signed in as</p>
                            <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">{{ auth()->user()->email ?? '' }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium transition">⚙️ Profile Settings</a>
                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-gray-700 font-semibold transition">🚪 Log Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container (Sidebar + Page Content) -->
    <div class="flex min-h-[calc(100vh-4rem)]">

        <!-- 📌 Sidebar -->
        <aside class="w-72 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col justify-between p-4 no-print shrink-0 md:sticky md:top-16 md:h-[calc(100vh-4rem)]">
            <div class="space-y-6">
                <nav class="space-y-2">
                    <p class="px-3 pb-1 text-xs font-bold text-gray-400 uppercase tracking-wider">Navigation</p>
                    
                    <a href="{{ route('dashboard') }}" class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg {{ request()->routeIs('dashboard') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition text-left">
                        📦 Asset Inventory
                    </a>
                    <a href="{{ route('dashboard') }}" class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition text-left">
                        📸 QR & Barcode Scanner
                    </a>
                    <a href="{{ route('dashboard') }}" class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition text-left">
                        📊 Reports & Analytics
                    </a>

                    @if(auth()->check() && strtolower(trim(auth()->user()->role)) === 'admin')
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-1.5">
                        <p class="px-3 pb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Management</p>
                        
                        <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg {{ request()->routeIs('categories.*') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">📂 Categories</a>
                        <a href="{{ route('warehouses.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg {{ request()->routeIs('warehouses.*') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">🏢 Warehouses</a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg {{ request()->routeIs('admin.users.index') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">👥 Users</a>
                        <a href="{{ route('admin.users.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg {{ request()->routeIs('admin.users.create') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">➕ Add Staff</a>
                        <a href="{{ route('activity.logs') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg {{ request()->routeIs('activity.logs') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">📋 Activity Logs</a>
                    </div>
                    @endif
                </nav>
            </div>
        </aside>

        <!-- Main Content Body -->
        <main class="flex-1 pt-8">
            <div class="max-w-5xl mx-auto space-y-6 px-4 pb-12">
                
                <!-- Navigation & Dual Print Buttons -->
                <div class="flex flex-wrap justify-between items-center gap-2 no-print">
                    <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition text-sm font-semibold shadow-sm">← Back</a>

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

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <span class="block text-xs font-medium text-gray-400">Category</span>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $asset->category->name ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-400">Warehouse Location</span>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $asset->warehouse->name ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-400">Assigned Employee</span>
                            @if($asset->assignee)
                                {{ $asset->assignee->name }}
                            @elseif($asset->items->whereNotNull('assigned_to')->count() > 0)
                                <!-- Serial တွေထဲမှာ Assign လုပ်ထားတာ ရှိရင် အဲ့ဒီနာမည်တွေကို စုပြရန် -->
                                {{ $asset->items->filter(fn($item) => $item->assignee)->map(fn($item) => $item->assignee->name)->unique()->join(', ') }}
                            @else
                                <span class="text-indigo-400">Unassigned</span>
                            @endif
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
        <!-- 🖥️ 1. SCREEN TABLE (မျက်နှာပြင်တွင် ၅ ခုစီသာ Page ခွဲပြရန် - $items) -->
        <table class="w-full text-left border-collapse border border-gray-200 dark:border-gray-700 text-sm text-center screen-table">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-900/80 border-b border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                    <th class="p-3 border border-gray-200 dark:border-gray-700">Serial Number</th>
                    <th class="p-3 border border-gray-200 dark:border-gray-700">Item QR Code</th>
                    <th class="p-3 border border-gray-200 dark:border-gray-700">Status</th>
                    <th class="p-3 border border-gray-200 dark:border-gray-700">Assigned Employee</th>
                    <th class="p-3 border border-gray-200 dark:border-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                @forelse($items as $item)
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

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        @if(auth()->user()->role === 'admin')
                            <form action="{{ route('asset-items.assign', $item->id) }}" method="POST" class="flex justify-center items-center w-full">
                                @csrf
                                <select name="assigned_to" onchange="this.form.submit()" class="text-xs rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 py-1.5 px-3 w-full max-w-[160px] text-center shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Unassigned --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $item->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @else
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $item->assignee->name ?? '-- Unassigned --' }}</span>
                        @endif
                    </td>

                    <td class="p-3 border border-gray-200 dark:border-gray-700">
    @if($item->status == 'available' || $item->status == 'maintenance')
        <a href="{{ route('assets.maintenances.index', $item->id) }}" class="inline-block bg-slate-700 hover:bg-slate-800 text-white font-semibold py-1.5 px-3 rounded text-xs transition shadow-sm">
            🛠️ Maintenance
        </a>
    @elseif($item->status == 'failed')
        <a href="{{ route('assets.maintenances.index', $item->id) }}" class="inline-block bg-amber-500 hover:bg-amber-600 text-white font-semibold py-1.5 px-3 rounded text-xs transition shadow-sm">
            🛠️ Retry Maintenance
        </a>
    @else
        <span class="text-xs text-gray-400 dark:text-gray-500 italic">Checked Out</span>
    @endif
</td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-6 border border-gray-200 dark:border-gray-700 text-center text-gray-400">No individual items registered.</td></tr>
                @endforelse
            </tbody>
        </table>

       <!-- 🖨️ 2. PRINT TABLE (Assigned Employee ပါဝင်သော Items အားလုံး $allItems) -->
<table class="w-full text-left border-collapse border border-gray-200 text-sm text-center print-table" style="display: none;">
    <thead>
        <tr>
            <th>Serial Number</th>
            <th>Item QR Code</th>
            <th>Status</th>
            <th>Assigned Employee</th>
        </tr>
    </thead>
    <tbody>
        @foreach($allItems as $item)
        <tr>
            <td>{{ $item->serial_number }}</td>
            <td>{!! $item->qr_code !!}</td>
            <td>{{ ucfirst($item->status) }}</td>
            <td>{{ $item->assignee->name ?? '-- Unassigned --' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
    </div>

    <!-- 💡 Pagination Links (Screen တွင်သာပြရန်) -->
    <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700 screen-pagination">
        {{ $items->links() }}
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
                        @if(auth()->user()->role === 'admin')
                        <button @click="activeTab = 'transfer'" 
                         :class="activeTab === 'transfer' ? 'border-amber-500 text-amber-600 dark:text-amber-400 bg-white dark:bg-gray-800 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                         class="px-4 py-2.5 text-sm font-medium rounded-t-lg transition flex items-center gap-2">
                       🚚 Warehouse Transfer
                       </button>
                       @endif
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

                    <!-- 2. 👤 Employee Assignment Section (NEW TAB) -->
                    <div x-show="activeTab === 'assign'" x-transition 
                         class="bg-white dark:bg-gray-800 p-6 rounded-b-lg rounded-tr-lg shadow-md space-y-4 border border-gray-100 dark:border-gray-700 transition-colors duration-200"
                         style="display: none;">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">👤 Assign Asset to Employee</h2>

                        @if(session('success'))
                            <div class="bg-green-100 dark:bg-green-900/50 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded text-sm">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('assets.assign', $asset->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Employee / User</label>
                                <select name="assigned_to" id="assigned_to" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 rounded-md p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Unassigned --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $asset->assigned_to == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md text-sm transition shadow-sm">
                                    Update Assignment
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- 3. 🚚 Asset Transfer Card -->
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

                    <!-- 4. Transaction History Table -->
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
        </main>
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