<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DummyJsonService
{
    protected $baseUrl = 'https://dummyjson.com';

    /**
     * Fetch all products from API
     */
    public function getProducts()
    {
        return Cache::remember('api_products', 300, function () {
            $response = Http::get("{$this->baseUrl}/products?limit=100");
            return $response->successful() ? $response->json()['products'] : [];
        });
    }

    /**
     * Fetch all users (as customers) from API
     */
    public function getUsers()
    {
        return Cache::remember('api_users', 300, function () {
            $response = Http::get("{$this->baseUrl}/users?limit=100");
            return $response->successful() ? $response->json()['users'] : [];
        });
    }

    /**
     * Fetch all carts (as orders) from API
     */
    public function getCarts()
    {
        return Cache::remember('api_carts', 300, function () {
            $response = Http::get("{$this->baseUrl}/carts?limit=100");
            return $response->successful() ? $response->json()['carts'] : [];
        });
    }

    /**
     * Get product categories
     */
    public function getCategories()
    {
        return Cache::remember('api_categories', 300, function () {
            $response = Http::get("{$this->baseUrl}/products/categories");
            return $response->successful() ? $response->json() : [];
        });
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        $products = $this->getProducts();
        $users = $this->getUsers();
        $carts = $this->getCarts();
        $categories = $this->getCategories();

        // Calculate total revenue from carts
        $totalRevenue = collect($carts)->sum('total');
        
        // Total orders
        $totalOrders = count($carts);
        
        // Total customers
        $totalCustomers = count($users);
        
        // Total products
        $totalProducts = count($products);

        // Sales by category
        $salesByCategory = collect($products)->groupBy('category')->map(function ($items, $category) {
            return [
                'category' => ucfirst($category),
                'total' => $items->sum('price') * rand(10, 50), // Simulate sales
                'count' => $items->count()
            ];
        })->values();

        // Top 10 products by rating (simulating best sellers)
        $topProducts = collect($products)
            ->sortByDesc('rating')
            ->take(10)
            ->map(function ($product) {
                return [
                    'product' => $product['title'],
                    'total_sales' => $product['price'] * rand(20, 100),
                    'total_qty' => rand(50, 200)
                ];
            })->values();

        // Order status distribution (simulated)
        $orderStatus = collect([
            ['status' => 'completed', 'count' => intval($totalOrders * 0.6)],
            ['status' => 'processing', 'count' => intval($totalOrders * 0.2)],
            ['status' => 'pending', 'count' => intval($totalOrders * 0.15)],
            ['status' => 'cancelled', 'count' => intval($totalOrders * 0.05)],
        ]);

        // Top 5 customers
        $topCustomers = collect($users)
            ->take(5)
            ->map(function ($user) use ($carts) {
                $userCarts = collect($carts)->where('userId', $user['id']);
                return [
                    'customer' => $user['firstName'] . ' ' . $user['lastName'],
                    'total_spent' => $userCarts->sum('total') ?: rand(1000, 10000),
                    'order_count' => $userCarts->count() ?: rand(1, 10)
                ];
            })->sortByDesc('total_spent')->values();

        // Recent orders (from carts)
        $recentOrders = collect($carts)
            ->take(10)
            ->map(function ($cart) use ($users) {
                $user = collect($users)->firstWhere('id', $cart['userId']);
                return [
                    'order_number' => 'ORD-' . str_pad($cart['id'], 4, '0', STR_PAD_LEFT),
                    'customer_name' => $user ? $user['firstName'] . ' ' . $user['lastName'] : 'Unknown',
                    'order_date' => now()->subDays(rand(1, 30))->format('d M Y'),
                    'total_amount' => $cart['total'],
                    'status' => ['completed', 'processing', 'pending'][rand(0, 2)]
                ];
            });

        // Monthly sales (simulated for last 12 months)
        $monthlySales = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlySales->push([
                'year' => $date->year,
                'month' => $date->month,
                'total' => rand(50000, 200000)
            ]);
        }

        // Daily sales (simulated for last 30 days)
        $dailySales = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailySales->push([
                'date' => $date->format('Y-m-d'),
                'total' => rand(5000, 30000)
            ]);
        }

        return [
            'totalRevenue' => $totalRevenue * 10, // Scale up for demo
            'totalOrders' => $totalOrders,
            'totalCustomers' => $totalCustomers,
            'totalProducts' => $totalProducts,
            'salesByCategory' => $salesByCategory,
            'topProducts' => $topProducts,
            'orderStatus' => $orderStatus,
            'topCustomers' => $topCustomers,
            'recentOrders' => $recentOrders,
            'monthlySales' => $monthlySales,
            'dailySales' => $dailySales,
            'categories' => $categories,
        ];
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        Cache::forget('api_products');
        Cache::forget('api_users');
        Cache::forget('api_carts');
        Cache::forget('api_categories');
    }
}
