<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sales Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
        .sidebar.collapsed {
            transform: translateX(-100%);
        }
        .main-content {
            transition: margin-left 0.3s ease-in-out;
        }
        .main-content.expanded {
            margin-left: 0 !important;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar w-64 bg-slate-800 min-h-screen fixed z-20">
            <div class="p-4 flex justify-between items-center">
                <h1 class="text-white text-xl font-bold whitespace-nowrap">
                    <i class="fas fa-chart-line mr-2"></i>Dashboard
                </h1>
                <button id="closeSidebar" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Data Source Toggle -->
            <div class="px-4 mb-4">
                <p class="text-gray-400 text-xs uppercase mb-2">Data Source</p>
                <div class="flex bg-slate-700 rounded-lg p-1">
                    <a href="{{ route('dashboard') }}" class="flex-1 text-center py-1 px-2 text-xs rounded {{ request()->routeIs('dashboard') || request()->routeIs('categories.*') || request()->routeIs('products.*') || request()->routeIs('customers.*') || request()->routeIs('orders.*') ? 'bg-blue-500 text-white' : 'text-gray-300 hover:text-white' }}">
                        Database
                    </a>
                    <a href="{{ route('weather.dashboard') }}" class="flex-1 text-center py-1 px-2 text-xs rounded {{ request()->routeIs('weather.*') ? 'bg-blue-500 text-white' : 'text-gray-300 hover:text-white' }}">
                        Weather API
                    </a>
                </div>
            </div>

            <nav class="mt-2">
                @if(request()->routeIs('weather.*'))
                    <!-- Weather API Navigation -->
                    <a href="{{ route('weather.dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('weather.dashboard') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                        <i class="fas fa-cloud-sun w-6"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('weather.cities') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('weather.cities') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                        <i class="fas fa-city w-6"></i>
                        <span>Cities</span>
                    </a>
                    <a href="{{ route('weather.forecast') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('weather.forecast') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                        <i class="fas fa-calendar-week w-6"></i>
                        <span>7-Day Forecast</span>
                    </a>
                    <a href="{{ route('weather.comparison') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('weather.comparison') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                        <i class="fas fa-balance-scale w-6"></i>
                        <span>Compare Cities</span>
                    </a>
                @else
                    <!-- Database Navigation -->
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('dashboard') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                        <i class="fas fa-home w-6"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('categories.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('categories.*') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                        <i class="fas fa-tags w-6"></i>
                        <span>Categories</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('products.*') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                        <i class="fas fa-box w-6"></i>
                        <span>Products</span>
                    </a>
                    <a href="{{ route('customers.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('customers.*') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                        <i class="fas fa-users w-6"></i>
                        <span>Customers</span>
                    </a>
                    <a href="{{ route('orders.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('orders.*') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                        <i class="fas fa-shopping-cart w-6"></i>
                        <span>Orders</span>
                    </a>
                @endif
            </nav>

            <!-- API Info -->
            @if(request()->routeIs('weather.*'))
            <div class="absolute bottom-4 left-4 right-4">
                <div class="bg-slate-700 rounded-lg p-3 text-xs">
                    <p class="text-gray-400 mb-1"><i class="fas fa-info-circle mr-1"></i> Data Source:</p>
                    <a
                        href="https://open-meteo.com/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-blue-400 hover:text-blue-300 underline"
                    >
                        Open-Meteo API (Free)
                    </a>
                </div>
            </div>
            @endif
        </aside>

        <!-- Overlay for mobile -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-10 hidden"></div>

        <!-- Main Content -->
        <main id="mainContent" class="main-content flex-1 ml-64 p-6 min-h-screen">
            <!-- Top Bar with Toggle -->
            <div class="flex items-center justify-between mb-6">
                <button id="toggleSidebar" class="bg-slate-800 text-white p-2 rounded-lg hover:bg-slate-700 transition">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="flex items-center space-x-4">
                    @if(request()->routeIs('weather.*'))
                        <span class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-full">
                            <i class="fas fa-cloud mr-1"></i> Weather API Mode
                        </span>
                    @else
                        <span class="text-sm bg-green-100 text-green-700 px-3 py-1 rounded-full">
                            <i class="fas fa-database mr-1"></i> Database Mode
                        </span>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebar');
        const closeBtn = document.getElementById('closeSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        // Check localStorage for sidebar state
        const sidebarState = localStorage.getItem('sidebarCollapsed');
        if (sidebarState === 'true') {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }

        function toggleSidebar() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        toggleBtn.addEventListener('click', toggleSidebar);
        
        if (closeBtn) {
            closeBtn.addEventListener('click', toggleSidebar);
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                overlay.classList.add('hidden');
            });
        }

        // Handle responsive
        function handleResize() {
            if (window.innerWidth < 1024) {
                sidebar.classList.add('sidebar-collapsed');
                mainContent.classList.add('main-expanded');
            }
        }

        window.addEventListener('resize', handleResize);
    </script>

    @stack('scripts')
</body>
</html>