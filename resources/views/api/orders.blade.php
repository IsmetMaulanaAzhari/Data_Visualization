@extends('layouts.app')

@section('title', 'API Orders')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Orders</h1>
    <p class="text-gray-600">Data from DummyJSON API (Carts)</p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($orders as $order)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $order['order_number'] }}</td>
                <td class="px-6 py-4">{{ $order['customer'] }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $order['date'] }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">
                        {{ $order['products_count'] }} items
                    </span>
                </td>
                <td class="px-6 py-4 font-semibold">${{ number_format($order['total'], 2) }}</td>
                <td class="px-6 py-4">
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
@endsection