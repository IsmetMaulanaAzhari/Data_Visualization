@extends('layouts.app')

@section('title', 'API Dashboard - Sales Visualization')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
    <p class="text-gray-600">Visualisasi data dari DummyJSON API</p>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-full">
                <i class="fas fa-dollar-sign text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Revenue</p>
                <p class="text-2xl font-bold text-gray-800">${{ number_format($totalRevenue, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-full">
                <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Orders</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalOrders) }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-full">
                <i class="fas fa-users text-purple-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Customers</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalCustomers) }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-orange-100 rounded-full">
                <i class="fas fa-box text-orange-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Products</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalProducts) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Monthly Sales Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-line text-blue-500 mr-2"></i>Penjualan Bulanan (12 Bulan Terakhir)
        </h3>
        <canvas id="monthlySalesChart" height="200"></canvas>
    </div>

    <!-- Sales by Category -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-pie text-green-500 mr-2"></i>Penjualan per Kategori
        </h3>
        <canvas id="categoryChart" height="200"></canvas>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Top Products -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-trophy text-yellow-500 mr-2"></i>Top 10 Produk Terlaris
        </h3>
        <canvas id="topProductsChart" height="250"></canvas>
    </div>

    <!-- Order Status -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-tasks text-purple-500 mr-2"></i>Status Order
        </h3>
        <canvas id="orderStatusChart" height="250"></canvas>
    </div>
</div>

<!-- Daily Sales & Top Customers -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Daily Sales (30 days) -->
    <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-calendar-alt text-indigo-500 mr-2"></i>Penjualan Harian (30 Hari Terakhir)
        </h3>
        <canvas id="dailySalesChart" height="150"></canvas>
    </div>

    <!-- Top Customers -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-star text-yellow-500 mr-2"></i>Top 5 Customers
        </h3>
        <div class="space-y-4">
            @foreach($topCustomers as $index => $customer)
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="w-6 h-6 rounded-full bg-blue-500 text-white text-xs flex items-center justify-center">{{ $index + 1 }}</span>
                    <span class="ml-3 text-gray-700">{{ $customer['customer'] }}</span>
                </div>
                <span class="text-sm font-semibold text-gray-800">${{ number_format($customer['total_spent'], 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-clock text-gray-500 mr-2"></i>Recent Orders
        </h3>
        <a href="{{ route('api.orders') }}" class="text-blue-500 hover:underline text-sm">View All</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-gray-500 text-sm border-b">
                    <th class="pb-3">Order Number</th>
                    <th class="pb-3">Customer</th>
                    <th class="pb-3">Date</th>
                    <th class="pb-3">Amount</th>
                    <th class="pb-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $order)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 font-medium">{{ $order['order_number'] }}</td>
                    <td class="py-3">{{ $order['customer_name'] }}</td>
                    <td class="py-3 text-gray-500">{{ $order['order_date'] }}</td>
                    <td class="py-3">${{ number_format($order['total_amount'], 2) }}</td>
                    <td class="py-3">
                        @switch($order['status'])
                            @case('completed')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Completed</span>
                                @break
                            @case('pending')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Pending</span>
                                @break
                            @case('processing')
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Processing</span>
                                @break
                            @case('cancelled')
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Cancelled</span>
                                @break
                        @endswitch
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Monthly Sales Chart
    const monthlySalesCtx = document.getElementById('monthlySalesChart').getContext('2d');
    new Chart(monthlySalesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlySales->map(fn($s) => date('M Y', mktime(0, 0, 0, $s['month'], 1, $s['year'])))) !!},
            datasets: [{
                label: 'Revenue ($)',
                data: {!! json_encode($monthlySales->pluck('total')) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($salesByCategory->pluck('category')) !!},
            datasets: [{
                data: {!! json_encode($salesByCategory->pluck('total')) !!},
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(245, 158, 11)',
                    'rgb(139, 92, 246)',
                    'rgb(236, 72, 153)',
                    'rgb(20, 184, 166)',
                    'rgb(249, 115, 22)',
                    'rgb(99, 102, 241)',
                    'rgb(34, 197, 94)',
                    'rgb(168, 85, 247)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Top Products Chart
    const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
    new Chart(topProductsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topProducts->pluck('product')->map(fn($p) => strlen($p) > 20 ? substr($p, 0, 20) . '...' : $p)) !!},
            datasets: [{
                label: 'Total Sales ($)',
                data: {!! json_encode($topProducts->pluck('total_sales')) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.8)'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Order Status Chart
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(orderStatusCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($orderStatus->pluck('status')->map(fn($s) => ucfirst($s))) !!},
            datasets: [{
                data: {!! json_encode($orderStatus->pluck('count')) !!},
                backgroundColor: [
                    'rgb(16, 185, 129)',
                    'rgb(59, 130, 246)',
                    'rgb(245, 158, 11)',
                    'rgb(239, 68, 68)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Daily Sales Chart
    const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
    new Chart(dailySalesCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($dailySales->pluck('date')->map(fn($d) => date('d M', strtotime($d)))) !!},
            datasets: [{
                label: 'Daily Revenue ($)',
                data: {!! json_encode($dailySales->pluck('total')) !!},
                backgroundColor: 'rgba(99, 102, 241, 0.8)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
@endpush