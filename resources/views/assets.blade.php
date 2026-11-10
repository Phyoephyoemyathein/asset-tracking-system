<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset & Warehouse Tracking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @media print {
            body { 
                background: white !important; 
                padding: 0 !important; 
                margin: 0 !important; 
                color: black !important; 
            }
            .no-print { 
                display: none !important; 
            }
            .print-shadow-none, .print-table-container { 
                box-shadow: none !important; 
                border: none !important; 
                width: 100% !important; 
                max-width: 100% !important; 
                margin: 0 !important; 
                padding: 0 !important; 
            }
            #print-table {
                width: 100% !important;
                border-collapse: separate !important;
                border-spacing: 0 !important;
            }
            #print-table th, 
            #print-table td {
                box-shadow: inset 0 0 0 1px #111827 !important;
                padding: 10px !important;
                text-align: left !important;
            }
            #print-table th {
                background-color: #f3f4f6 !important;
                color: #1f2937 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            no-print {
            display: none !important;
        }

        .print-only {
            display: block !important;
        }
        }

        .print-only {
        display: none;
    }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 min-h-screen transition-colors duration-200" x-data="{ activeTab: 'dashboard', sidebarOpen: false }">

    <div class="min-h-screen flex flex-col">

        <!-- 🌐 Top Navigation Bar -->
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm no-print h-16 shrink-0 z-30 sticky top-0">
            <div class="px-4 sm:px-6 lg:px-8 h-full flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-600 dark:text-gray-300 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <span class="text-base sm:text-lg font-extrabold text-gray-800 dark:text-gray-100">📦 Asset & Warehouse Tracking System</span>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="toggleDarkMode()" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 p-2 rounded-lg text-xs font-medium text-gray-700 dark:text-gray-200 transition focus:outline-none flex items-center gap-1">
                        <span id="themeIcon">🌙</span>
                    </button>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 px-3 py-1.5 rounded-lg text-xs font-medium text-gray-700 dark:text-gray-200 transition focus:outline-none">
                            <span class="font-bold text-gray-900 dark:text-gray-100 truncate">👤 {{ auth()->user()->name }}</span>
                            <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-[10px] font-semibold px-1.5 py-0.5 rounded-full uppercase">
                                {{ auth()->user()->role }}
                            </span>
                            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-1 z-50" style="display: none;">
                            <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Signed in as</p>
                                <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">{{ auth()->user()->email }}</p>
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

        <!-- Main Body Layout (Sidebar + Content Container) -->
        <div class="flex-1 flex overflow-hidden relative">

            <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-20 md:hidden" style="display: none;"></div>

            <!-- 📌 Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-40 w-72 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col justify-between p-4 no-print shrink-0 overflow-y-auto transition-transform duration-300 md:translate-x-0 md:static md:h-[calc(100vh-4rem)]">
    <div class="space-y-6">
        <nav class="space-y-2">
            <p class="px-3 pb-1 text-xs font-bold text-gray-400 uppercase tracking-wider">NAVIGATION</p>
            
            <button @click="activeTab = 'dashboard'; sidebarOpen = false;" :class="activeTab === 'dashboard' ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg transition text-left">
                📦 Asset Inventory
            </button>
            
            <button @click="activeTab = 'scanners'; sidebarOpen = false; setTimeout(initScanner, 100);" :class="activeTab === 'scanners' ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg transition text-left">
                📸 QR & Barcode Scanner
            </button>
            
            <button @click="activeTab = 'reports'; sidebarOpen = false;" :class="activeTab === 'reports' ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg transition text-left">
                📊 Reports & Analytics
            </button>

            @if(strtolower(trim(auth()->user()->role)) === 'admin')
            <div class="pt-5 border-t border-gray-200 dark:border-gray-700 space-y-1.5 mt-4">
                <p class="px-3 pb-1 text-xs font-bold text-gray-400 uppercase tracking-wider">MANAGEMENT</p>
                <a href="{{ route('categories.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">📂 Categories</a>
                <a href="{{ route('warehouses.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">🏢 Warehouses</a>
                <a href="{{ route('admin.users.index') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">👥 Users</a>
                <a href="{{ route('admin.users.create') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">➕ Add Staff</a>
                <a href="{{ route('activity.logs') }}" @click="sidebarOpen = false" class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">📋 Activity Logs</a>
            </div>
            @endif
        </nav>
    </div>
</aside>

            <!-- 🖥️ Main Workspace Content -->
            <main class="flex-1 overflow-y-auto p-6 space-y-6">
                <div class="max-w-6xl mx-auto space-y-8 pb-12">

                    @if(session('success'))
                    <div class="bg-green-100 dark:bg-green-900/40 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded no-print">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="bg-red-100 dark:bg-red-900/40 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded no-print">
                        <strong class="font-bold">Please fix the following errors:</strong>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- 1️⃣ TAB: Asset Inventory & Dashboard Main -->
                    <div x-show="activeTab === 'dashboard'" class="space-y-6">
                        @if(isset($lowStockAssets) && $lowStockAssets->count() > 0)
                        <div class="bg-amber-50 dark:bg-amber-900/40 border-l-4 border-amber-500 p-4 rounded-lg shadow-md no-print">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">⚠️</span>
                                    <div>
                                        <h3 class="text-sm font-bold text-amber-800 dark:text-amber-200">Low Stock Warning!</h3>
                                        <p class="text-xs text-amber-700 dark:text-amber-300">There are {{ $lowStockAssets->count() }} asset(s) running at or below the minimum stock level.</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($lowStockAssets as $lowAsset)
                                        <a href="{{ route('assets.show', $lowAsset->id) }}" class="bg-amber-600 hover:bg-amber-700 text-white text-xs px-2.5 py-1 rounded font-semibold transition shadow-sm">
                                            {{ $lowAsset->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 no-print">
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border-l-4 border-blue-500">
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold">Total Assets</p>
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalAssets ?? 0 }}</h3>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border-l-4 border-green-500">
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold">Total Quantity</p>
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalQuantity ?? 0 }}</h3>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border-l-4 border-yellow-500">
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold">Categories</p>
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalCategories ?? 0 }}</h3>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border-l-4 border-purple-500">
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold">Warehouses</p>
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalWarehouses ?? 0 }}</h3>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md space-y-6 print-shadow-none print-table-container">
                            <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-4">
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Asset Inventory & QR Codes</h2>
                            </div>

                            @if(strtolower(trim(auth()->user()->role)) === 'admin')
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700 no-print flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <button onclick="openRegisterModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-1.5 rounded text-xs font-semibold transition shadow-sm flex items-center gap-1 whitespace-nowrap">
                                        ➕ Register Asset
                                    </button>
                                    <a href="{{ route('assets.sample-csv') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-3 py-1.5 rounded text-xs font-semibold transition flex items-center gap-1 border border-gray-300 dark:border-gray-600 whitespace-nowrap">
                                        📥 Sample CSV
                                    </a>
                                </div>
                                <form action="{{ route('assets.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-end">
                                    @csrf
                                    <input type="file" name="file" accept=".csv, .txt, .xlsx" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-1 rounded text-xs" required>
                                    <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-3.5 py-1.5 rounded text-xs font-semibold transition shadow-sm whitespace-nowrap">
                                        📤 Import File
                                    </button>
                                </form>
                            </div>
                            @endif

                            <form action="" method="GET" class="flex flex-wrap gap-2.5 items-center no-print bg-gray-50 dark:bg-gray-700/40 p-3.5 rounded-lg border border-gray-200 dark:border-gray-700">
                                <select name="category_id" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 rounded-lg text-xs bg-white focus:ring-2 focus:ring-indigo-500">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>

                                <select name="warehouse_id" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 rounded-lg text-xs bg-white focus:ring-2 focus:ring-indigo-500">
                                    <option value="">All Warehouses</option>
                                    @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>

                                <input type="date" name="start_date" value="{{ request('start_date') }}" title="Start Date" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500">
                                <input type="date" name="end_date" value="{{ request('end_date') }}" title="End Date" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or SKU..." class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 rounded-lg text-xs flex-1 min-w-[140px] focus:ring-2 focus:ring-indigo-500">

                                <button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white px-3.5 py-2 rounded-lg text-xs font-semibold transition shadow-sm">🔍 Filter</button>

                                @if(request('search') || request('category_id') || request('warehouse_id') || request('start_date') || request('end_date'))
                                <a href="{{ url()->current() }}" class="bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 px-3 py-2 rounded-lg text-xs font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition">Reset</a>
                                @endif

                                <div class="h-5 w-[1px] bg-gray-300 dark:bg-gray-600 mx-1 hidden xl:block"></div>
                                <a href="{{ route('assets.export', request()->all()) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded-lg text-xs font-semibold flex items-center gap-1 transition shadow-sm">📊 Export Excel</a>
                                <button type="button" onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-xs font-semibold no-print flex items-center gap-1 transition shadow-sm">🖨️ Print Current View</button>

                                <a href="{{ route('assets.printAll') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg text-xs font-semibold no-print flex items-center gap-1 transition shadow-sm">🖨️ Print All Assets</a>
                            </form>

                            <div class="overflow-x-auto table-wrapper">
                                <table id="print-table" class="w-full border-collapse border border-gray-200 dark:border-gray-700">
                                    <thead>
                                        <tr class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200 font-semibold">
                                            <th class="p-3 border border-gray-200 dark:border-gray-600">Asset Name</th>
                                            <th class="p-3 border border-gray-200 dark:border-gray-600">SKU</th>
                                            <th class="p-3 border border-gray-200 dark:border-gray-600">Category</th>
                                            <th class="p-3 border border-gray-200 dark:border-gray-600">Warehouse</th>
                                            <th class="p-3 border border-gray-200 dark:border-gray-600">Quantity</th>
                                            <th class="p-3 border border-gray-200 dark:border-gray-600">Model QR Code</th>
                                            @if(strtolower(trim(auth()->user()->role)) === 'admin')
                                            <th class="p-3 border border-gray-200 dark:border-gray-600 no-print">Actions</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($assets as $asset)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="border border-gray-200 dark:border-gray-700 p-3 font-semibold">
                                                <a href="{{ route('assets.show', $asset->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $asset->name }}</a>
                                            </td>
                                            <td class="border border-gray-200 dark:border-gray-700 p-3 text-gray-600 dark:text-gray-400">{{ $asset->sku }}</td>
                                            <td class="border border-gray-200 dark:border-gray-700 p-3 text-gray-700 dark:text-gray-300">{{ $asset->category->name ?? '-' }}</td>
                                            <td class="border border-gray-200 dark:border-gray-700 p-3 text-gray-700 dark:text-gray-300">{{ $asset->warehouse->name ?? '-' }}</td>
                                            <td class="border border-gray-200 dark:border-gray-700 p-3 text-center">
                                                <span class="{{ $asset->available_items_count <= $asset->min_stock_level ? 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300 px-2 py-1 rounded font-bold inline-block' : 'text-gray-700 dark:text-gray-300' }}">
                                                    <span class="font-semibold text-green-600 dark:text-green-400">{{ $asset->available_items_count ?? 0 }}</span> 
                                                    <span class="text-gray-400 mx-1">/</span> 
                                                    <span>{{ $asset->items_count ?? 0 }}</span>
                                                    @if($asset->available_items_count <= $asset->min_stock_level)
                                                        <span class="block mt-1 text-xs">⚠️ Low Stock!</span>
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="border border-gray-200 dark:border-gray-700 p-3 text-center align-middle cursor-pointer group" onclick="openQrModal(this)" title="Click to view large QR code">
                                                <div class="inline-block w-16 h-16 [&>svg]:w-full [&>svg]:h-full pointer-events-none transition transform group-hover:scale-105 rounded border border-transparent group-hover:border-indigo-400 p-0.5 shadow-sm bg-white">
                                                    {!! $asset->qr_code !!}
                                                </div>
                                                <div class="no-print text-[10px] text-indigo-600 dark:text-indigo-400 font-medium mt-1 group-hover:underline">🔍Click to view</div>
                                            </td>
                                            @if(strtolower(trim(auth()->user()->role)) === 'admin')                                        
                                            <td class="border border-gray-200 dark:border-gray-700 p-3 text-center align-middle no-print">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ route('assets.edit', $asset->id) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition shadow-sm">✏️ Edit</a>
                                                    <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('Are you sure you want to delete this asset?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition shadow-sm">🗑️ Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center p-4 text-gray-500 dark:text-gray-400">No assets found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700">
                                {{ $assets->links() }}
                            </div>
                        </div>
                    </div>

                    <!-- 2️⃣ TAB: QR & Barcode Scanners -->
                    <div x-show="activeTab === 'scanners'" class="space-y-6" style="display: none;">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md no-print border-l-4 border-indigo-500">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">📷 Quick QR / SKU Lookup</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Enter or paste the scanned SKU below to navigate directly to the asset's detail page.</p>
                            <form action="{{ route('assets.scan') }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="text" name="sku" placeholder="Scan or enter SKU here (e.g., GEN-001-2026)..." class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2 rounded text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded text-sm font-semibold whitespace-nowrap transition shadow-sm">View Asset</button>
                            </form>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md no-print border-l-4 border-emerald-500 mt-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">📸 Live Camera Barcode / QR Scanner</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">"Use your phone camera or webcam to directly scan the QR Code / Barcode."</p>
                            <div id="reader" class="w-full max-w-md mx-auto rounded overflow-hidden"></div>
                        </div>
                    </div>

                    <!-- 3️⃣ TAB: Advanced PDF Reports & Analytics Charts -->
                    <div x-show="activeTab === 'reports'" class="space-y-6" style="display: none;">
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md no-print border-l-4 border-rose-500 flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">📊 Advanced PDF Reports</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Generate and download professional PDF summaries and movement logs.</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('reports.stock-summary') }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 rounded-lg text-xs font-semibold transition shadow-sm flex items-center gap-1.5">📄 Stock Summary PDF</a>
                                <a href="{{ route('reports.movement', ['type' => 'monthly']) }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-lg text-xs font-semibold transition shadow-sm flex items-center gap-1.5">📊 Monthly Movement PDF</a>
                                <a href="{{ route('reports.movement', ['type' => 'yearly']) }}" target="_blank" class="bg-amber-600 hover:bg-amber-700 text-white px-3.5 py-2 rounded-lg text-xs font-semibold transition shadow-sm flex items-center gap-1.5">📈 Yearly Movement PDF</a>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 no-print">
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">📦 Stock by Category</h3>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded font-semibold">Quantity</span>
                                </div>
                                <div class="relative h-64">
                                    <canvas id="categoryChart"></canvas>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">🏢 Stock by Warehouse</h3>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded font-semibold">Distribution</span>
                                </div>
                                <div class="relative h-64">
                                    <canvas id="warehouseChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>





    <!-- html5-qrcode Library CDN -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <!-- Register Asset Pop-up Modal -->
    @if(strtolower(trim(auth()->user()->role)) === 'admin')
    <div id="registerModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 hidden no-print" onclick="closeRegisterModal()">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-2xl max-w-2xl w-full relative mx-4" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Register New Asset & Generate QR</h2>
                <button onclick="closeRegisterModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold text-lg">&times;</button>
            </div>
            <form action="{{ route('assets.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-1 text-xs">Category</label>
                    <select name="category_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2 rounded text-xs" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-1 text-xs">Warehouse</label>
                    <select name="warehouse_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2 rounded text-xs" required>
                        <option value="">Select Warehouse</option>
                        @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-1 text-xs">Asset Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2 rounded text-xs" required>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-1 text-xs">SKU / Serial Number</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2 rounded text-xs" required>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-1 text-xs">Quantity</label>
                    <input type="number" name="quantity" value="{{ old('quantity', 1) }}" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2 rounded text-xs" required>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-1 text-xs">Minimum Stock Level</label>
                    <input type="number" name="min_stock_level" value="{{ old('min_stock_level', 5) }}" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2 rounded text-xs" required>
                </div>
                <div class="md:col-span-2 flex justify-end gap-2 mt-2">
                    <button type="button" onclick="closeRegisterModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-xs font-semibold transition">Cancel</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-xs font-semibold transition shadow-sm">Save & Generate QR Code</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- QR Code Modal Pop-up -->
    <div id="qrModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 hidden no-print" onclick="closeQrModal()">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-2xl max-w-sm w-full text-center relative mx-4" onclick="event.stopPropagation()">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">QR Code Preview</h3>
            <div id="modalQrContainer" class="flex justify-center items-center my-4 [&>svg]:w-64 [&>svg]:h-64 bg-white p-4 rounded-lg"></div>
            <button onclick="closeQrModal()" class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold transition shadow-sm">Close</button>
        </div>
    </div>

    <script>
        function openRegisterModal() {
            const modal = document.getElementById('registerModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeRegisterModal() {
            const modal = document.getElementById('registerModal');
            if (modal) modal.classList.add('hidden');
        }

        function openQrModal(element) {
            const qrSvg = element.querySelector('div').innerHTML;
            document.getElementById('modalQrContainer').innerHTML = qrSvg;
            document.getElementById('qrModal').classList.remove('hidden');
        }

        function closeQrModal() {
            document.getElementById('qrModal').classList.add('hidden');
        }

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

        // 1. Category Bar Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($categoryLabels ?? []) !!},
                datasets: [{
                    label: 'Quantity',
                    data: {!! json_encode($categoryQuantities ?? []) !!},
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // 2. Warehouse Doughnut Chart
        const warehouseCtx = document.getElementById('warehouseChart').getContext('2d');
        new Chart(warehouseCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($warehouseLabels ?? []) !!},
                datasets: [{
                    data: {!! json_encode($warehouseQuantities ?? []) !!},
                    backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                cutout: '65%'
            }
        });

        // Live Camera Scanner Setup
        let html5QrcodeScanner = null;
        function initScanner() {
            const readerElement = document.getElementById("reader");
            if (readerElement && !html5QrcodeScanner) {
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", 
                    { 
                        fps: 10, 
                        qrbox: { width: 250, height: 250 },
                        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA] 
                    },
                    false
                );
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            console.log(`Scan result: ${decodedText}`, decodedResult);
            const skuInput = document.querySelector('div[x-show="activeTab === \'scanners\'"] input[name="sku"]');
            if (skuInput) {
                skuInput.value = decodedText;
                skuInput.form.submit();
            }
        }

        function onScanFailure(error) {}
    </script>

</body>
</html>