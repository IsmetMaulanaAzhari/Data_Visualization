@extends('layouts.app')

@section('title', 'API Products')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Products</h1>
    <p class="text-gray-600">Data from DummyJSON API</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @foreach($products as $product)
    <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
        <img src="{{ $product['thumbnail'] }}" alt="{{ $product['title'] }}" class="w-full h-48 object-cover">
        <div class="p-4">
            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded-full">{{ ucfirst($product['category']) }}</span>
            <h3 class="font-semibold text-gray-800 mt-2 truncate">{{ $product['title'] }}</h3>
            <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $product['description'] }}</p>
            
            <div class="flex justify-between items-center mt-4">
                <div>
                    <span class="text-lg font-bold text-green-600">${{ number_format($product['price'], 2) }}</span>
                    @if($product['discountPercentage'] > 0)
                        <span class="text-xs text-red-500 ml-1">-{{ $product['discountPercentage'] }}%</span>
                    @endif
                </div>
                <div class="flex items-center text-yellow-500">
                    <i class="fas fa-star text-sm"></i>
                    <span class="ml-1 text-gray-600 text-sm">{{ $product['rating'] }}</span>
                </div>
            </div>
            
            <div class="flex justify-between items-center mt-3 text-sm text-gray-500">
                <span><i class="fas fa-box mr-1"></i>Stock: {{ $product['stock'] }}</span>
                <span class="text-xs px-2 py-1 {{ $product['stock'] > 50 ? 'bg-green-100 text-green-700' : ($product['stock'] > 10 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }} rounded">
                    {{ $product['stock'] > 50 ? 'In Stock' : ($product['stock'] > 10 ? 'Low Stock' : 'Critical') }}
                </span>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection