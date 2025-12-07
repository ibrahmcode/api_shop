<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function index()
    {
        $stats = [
            // Users stats
            'total_users' => User::where('role', 'user')->count(),
            'new_users_today' => User::where('role', 'user')->whereDate('created_at', today())->count(),
            'new_users_this_week' => User::where('role', 'user')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'new_users_this_month' => User::where('role', 'user')->whereMonth('created_at', now()->month)->count(),

            // Products stats
            'total_items' => Item::count(),
            'total_categories' => Category::count(),
            
            // Orders stats
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),

            // Revenue stats
            'total_revenue' => Order::whereIn('status', ['delivered'])->sum('total_amount'),
            'today_revenue' => Order::whereDate('created_at', today())->whereIn('status', ['delivered'])->sum('total_amount'),
            'this_week_revenue' => Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                                        ->whereIn('status', ['delivered'])
                                        ->sum('total_amount'),
            'this_month_revenue' => Order::whereMonth('created_at', now()->month)
                                         ->whereIn('status', ['delivered'])
                                         ->sum('total_amount'),

            // Today's stats
            'today_orders' => Order::whereDate('created_at', today())->count(),
            
            // Average order value
            'average_order_value' => Order::whereIn('status', ['delivered'])->avg('total_amount') ?? 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ], 200);
    }

    /**
     * Get monthly revenue chart data (last 12 months)
     */
    public function monthlyRevenue()
    {
        $monthlyData = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_amount) as revenue, COUNT(*) as orders')
                            ->whereIn('status', ['delivered'])
                            ->where('created_at', '>=', now()->subMonths(12))
                            ->groupBy('month')
                            ->orderBy('month')
                            ->get();

        return response()->json([
            'success' => true,
            'data' => $monthlyData
        ], 200);
    }

    /**
     * Get top selling items
     */
    public function topSellingItems(Request $request)
    {
        $limit = $request->get('limit', 10);

        $topItems = DB::table('order_items')
                    ->join('items', 'order_items.item_id', '=', 'items.id')
                    ->select('items.id', 'items.name', 'items.price', 'items.image',
                            DB::raw('SUM(order_items.quantity) as total_sold'),
                            DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'))
                    ->groupBy('items.id', 'items.name', 'items.price', 'items.image')
                    ->orderBy('total_sold', 'desc')
                    ->limit($limit)
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $topItems
        ], 200);
    }

    /**
     * Get recent orders
     */
    public function recentOrders(Request $request)
    {
        $limit = $request->get('limit', 10);

        $orders = Order::with(['user:id,name,email', 'items'])
                      ->orderBy('created_at', 'desc')
                      ->limit($limit)
                      ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ], 200);
    }

    /**
     * Get order status distribution
     */
    public function orderStatusDistribution()
    {
        $distribution = Order::selectRaw('status, COUNT(*) as count')
                             ->groupBy('status')
                             ->get();

        return response()->json([
            'success' => true,
            'data' => $distribution
        ], 200);
    }

    /**
     * Get top customers
     */
    public function topCustomers(Request $request)
    {
        $limit = $request->get('limit', 10);

        $customers = User::where('role', 'user')
                        ->withCount('orders')
                        ->withSum('orders', 'total_amount')
                        ->orderBy('orders_sum_total_amount', 'desc')
                        ->limit($limit)
                        ->get();

        return response()->json([
            'success' => true,
            'data' => $customers
        ], 200);
    }
}
