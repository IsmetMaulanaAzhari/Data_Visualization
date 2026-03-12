@extends('layouts.app')

@section('title', 'API Categories')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Categories</h1>
    <p class="text-gray-600">Data from DummyJSON API</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @foreach($categories as $category)
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
            <div class="p-3 bg-blue-100 rounded-full">
                <i class="fas fa-tag text-blue-600 text-xl"></i>
            </div>
            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                {{ $category['products_count'] }} products
            </span>
        </div>
        <h3 class="font-semibold text-gray-800 mt-4 text-lg">{{ $category['name'] }}</h3>
        <p class="text-sm text-gray-500 mt-1">{{ $category['slug'] }}</p>
    </div>
    @endforeach
</div>

<!-- Category Distribution Chart -->
<div class="mt-8 bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-chart-bar text-blue-500 mr-2"></i>Products per Category
    </h3>
    <canvas id="categoryChart" height="100"></canvas>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($categories->pluck('name')) !!},
            datasets: [{
                label: 'Products Count',
                data: {!! json_encode($categories->pluck('products_count')) !!},
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(20, 184, 166, 0.8)',
                    'rgba(249, 115, 22, 0.8)',
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(244, 63, 94, 0.8)',
                    'rgba(14, 165, 233, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush