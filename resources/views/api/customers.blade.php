@extends('layouts.app')

@section('title', 'API Customers')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Customers</h1>
    <p class="text-gray-600">Data from DummyJSON API</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($customers as $customer)
    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="flex items-center">
            <img src="{{ $customer['image'] }}" alt="{{ $customer['firstName'] }}" class="w-16 h-16 rounded-full object-cover">
            <div class="ml-4">
                <h3 class="font-semibold text-gray-800">{{ $customer['firstName'] }} {{ $customer['lastName'] }}</h3>
                <p class="text-sm text-gray-500">{{ $customer['email'] }}</p>
            </div>
        </div>
        
        <div class="mt-4 space-y-2">
            <div class="flex items-center text-sm text-gray-600">
                <i class="fas fa-phone w-5"></i>
                <span>{{ $customer['phone'] }}</span>
            </div>
            <div class="flex items-center text-sm text-gray-600">
                <i class="fas fa-map-marker-alt w-5"></i>
                <span>{{ $customer['address']['city'] }}, {{ $customer['address']['state'] }}</span>
            </div>
            <div class="flex items-center text-sm text-gray-600">
                <i class="fas fa-briefcase w-5"></i>
                <span>{{ $customer['company']['title'] }} at {{ $customer['company']['name'] }}</span>
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-t flex justify-between items-center">
            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">{{ $customer['gender'] }}</span>
            <span class="text-xs text-gray-500">Age: {{ $customer['age'] }}</span>
        </div>
    </div>
    @endforeach
</div>
@endsection