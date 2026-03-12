<?php

namespace App\Http\Controllers;

use App\Services\DummyJsonService;
use Illuminate\Http\Request;

class ApiDashboardController extends Controller
{
    protected $apiService;

    public function __construct(DummyJsonService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        $stats = $this->apiService->getDashboardStats();

        return view('dashboard.api', [
            'totalRevenue' => $stats['totalRevenue'],
            'totalOrders' => $stats['totalOrders'],
            'totalCustomers' => $stats['totalCustomers'],
            'totalProducts' => $stats['totalProducts'],
            'monthlySales' => $stats['monthlySales'],
            'salesByCategory' => $stats['salesByCategory'],
            'topProducts' => $stats['topProducts'],
            'orderStatus' => $stats['orderStatus'],
            'topCustomers' => $stats['topCustomers'],
            'recentOrders' => $stats['recentOrders'],
            'dailySales' => $stats['dailySales'],
        ]);
    }

    public function products()
    {
        $products = collect($this->apiService->getProducts());
        
        return view('api.products', [
            'products' => $products
        ]);
    }

    public function customers()
    {
        $users = collect($this->apiService->getUsers());
        
        return view('api.customers', [
            'customers' => $users
        ]);
    }

    public function orders()
    {
        $carts = collect($this->apiService->getCarts());
        $users = collect($this->apiService->getUsers());
        
        $orders = $carts->map(function ($cart) use ($users) {
            $user = $users->firstWhere('id', $cart['userId']);
            return [
                'id' => $cart['id'],
                'order_number' => 'ORD-' . str_pad($cart['id'], 4, '0', STR_PAD_LEFT),
                'customer' => $user ? $user['firstName'] . ' ' . $user['lastName'] : 'Unknown',
                'total' => $cart['total'],
                'products_count' => count($cart['products']),
                'status' => ['completed', 'processing', 'pending', 'cancelled'][rand(0, 3)],
                'date' => now()->subDays(rand(1, 60))->format('d M Y'),
            ];
        });

        return view('api.orders', [
            'orders' => $orders
        ]);
    }

    public function categories()
    {
        $categories = $this->apiService->getCategories();
        $products = collect($this->apiService->getProducts());

        $categoriesWithCount = collect($categories)->map(function ($category) use ($products) {
            return [
                'slug' => $category['slug'],
                'name' => $category['name'],
                'products_count' => $products->where('category', $category['slug'])->count()
            ];
        });

        return view('api.categories', [
            'categories' => $categoriesWithCount
        ]);
    }

    public function refreshCache()
    {
        $this->apiService->clearCache();
        return redirect()->back()->with('success', 'Data cache refreshed successfully!');
    }
}
