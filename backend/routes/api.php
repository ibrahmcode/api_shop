<?php

use App\Http\Controllers\Admin\AdminBannerController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminItemController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\User\UserCategoryController;
use App\Http\Controllers\User\UserItemController;
use App\Http\Controllers\User\UserNotificationController;
use App\Http\Controllers\User\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public - App Settings & Config
Route::get('settings', [SettingController::class, 'index']);
Route::get('settings/config', [SettingController::class, 'appConfig']);
Route::get('settings/{key}', [SettingController::class, 'get']);
Route::get('content/{page}', [SettingController::class, 'content']);
Route::get('languages', [SettingController::class, 'languages']);

// Public - Banners
Route::get('banners', [BannerController::class, 'index']);
Route::get('banners/{banner}', [BannerController::class, 'show']);

// Public - Categories and Items (read-only for guests)
Route::get('categories', [UserCategoryController::class, 'index']);
Route::get('categories/{category}', [UserCategoryController::class, 'show']);
Route::get('categories/{category}/items', [UserItemController::class, 'index']);
Route::get('categories/{category}/items/{item}', [UserItemController::class, 'show']);
Route::get('items/search', [UserItemController::class, 'search']);

// Public - Reviews (read-only)
Route::get('items/{item}/reviews', [ReviewController::class, 'index']);

// ========== ADMIN ROUTES ==========
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Admin - Categories Management
    Route::get('categories', [AdminCategoryController::class, 'index']);
    Route::post('categories', [AdminCategoryController::class, 'store']);
    Route::get('categories/{category}', [AdminCategoryController::class, 'show']);
    Route::put('categories/{category}', [AdminCategoryController::class, 'update']);
    Route::delete('categories/{category}', [AdminCategoryController::class, 'destroy']);

    // Admin - Items Management
    Route::get('categories/{category}/items', [AdminItemController::class, 'index']);
    Route::post('categories/{category}/items', [AdminItemController::class, 'store']);
    Route::get('categories/{category}/items/{item}', [AdminItemController::class, 'show']);
    Route::post('categories/{category}/items/{item}', [AdminItemController::class, 'update']);
    Route::delete('categories/{category}/items/{item}', [AdminItemController::class, 'destroy']);

    // Admin - Orders Management
    Route::get('orders', [AdminOrderController::class, 'index']);
    Route::get('orders/statistics', [AdminOrderController::class, 'statistics']);
    Route::get('orders/{order}', [AdminOrderController::class, 'show']);
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus']);
    Route::delete('orders/{order}', [AdminOrderController::class, 'destroy']);

    // Admin - Coupons Management
    Route::get('coupons', [AdminCouponController::class, 'index']);
    Route::post('coupons', [AdminCouponController::class, 'store']);
    Route::get('coupons/{coupon}', [AdminCouponController::class, 'show']);
    Route::put('coupons/{coupon}', [AdminCouponController::class, 'update']);
    Route::delete('coupons/{coupon}', [AdminCouponController::class, 'destroy']);
    Route::patch('coupons/{coupon}/toggle', [AdminCouponController::class, 'toggleStatus']);

    // Admin - Dashboard Statistics
    Route::get('dashboard', [AdminDashboardController::class, 'index']);
    Route::get('dashboard/monthly-revenue', [AdminDashboardController::class, 'monthlyRevenue']);
    Route::get('dashboard/top-selling-items', [AdminDashboardController::class, 'topSellingItems']);
    Route::get('dashboard/recent-orders', [AdminDashboardController::class, 'recentOrders']);
    Route::get('dashboard/order-status-distribution', [AdminDashboardController::class, 'orderStatusDistribution']);
    Route::get('dashboard/top-customers', [AdminDashboardController::class, 'topCustomers']);

    // Admin - Settings Management
    Route::get('settings', [AdminSettingController::class, 'index']);
    Route::get('settings/groups', [AdminSettingController::class, 'groups']);
    Route::get('settings/{key}', [AdminSettingController::class, 'show']);
    Route::put('settings/{key}', [AdminSettingController::class, 'update']);
    Route::post('settings/bulk-update', [AdminSettingController::class, 'bulkUpdate']);
    Route::post('settings/clear-cache', [AdminSettingController::class, 'clearCache']);

    // Admin - Banners Management
    Route::get('banners', [AdminBannerController::class, 'index']);
    Route::post('banners', [AdminBannerController::class, 'store']);
    Route::get('banners/{banner}', [AdminBannerController::class, 'show']);
    Route::post('banners/{banner}', [AdminBannerController::class, 'update']);
    Route::delete('banners/{banner}', [AdminBannerController::class, 'destroy']);
    Route::patch('banners/{banner}/toggle', [AdminBannerController::class, 'toggleStatus']);
    Route::post('banners/reorder', [AdminBannerController::class, 'reorder']);

    // Admin - Notifications Management
    Route::get('notifications', [AdminNotificationController::class, 'index']);
    Route::get('notifications/users', [AdminNotificationController::class, 'getUsers']);
    Route::post('notifications/send-to-user', [AdminNotificationController::class, 'sendToUser']);
    Route::post('notifications/send-to-all', [AdminNotificationController::class, 'sendToAll']);
});

// ========== USER ROUTES ==========
Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Profile Management
    Route::get('/profile', [UserProfileController::class, 'show']);
    Route::put('/profile', [UserProfileController::class, 'update']);
    Route::post('/profile/change-password', [UserProfileController::class, 'changePassword']);
    Route::post('/profile/avatar', [UserProfileController::class, 'uploadAvatar']);
    Route::delete('/profile/avatar', [UserProfileController::class, 'deleteAvatar']);
    
    // Cart Management
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart', [CartController::class, 'store']);
    Route::put('cart/{cart}', [CartController::class, 'update']);
    Route::delete('cart/{cart}', [CartController::class, 'destroy']);
    Route::post('cart/clear', [CartController::class, 'clear']);
    Route::get('cart/count', [CartController::class, 'count']);
    
    // Favorite Items routes
    Route::get('favorites', [FavoriteController::class, 'index']);
    Route::post('favorites', [FavoriteController::class, 'store']);
    Route::delete('favorites/{item}', [FavoriteController::class, 'destroy']);
    Route::get('favorites/check/{item}', [FavoriteController::class, 'check']);
    Route::post('favorites/toggle', [FavoriteController::class, 'toggle']);

    // Order routes
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::get('orders/{order}/tracking', [OrderController::class, 'tracking']);
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::delete('orders/{order}', [OrderController::class, 'destroy']);

    // Address Management
    Route::get('addresses', [AddressController::class, 'index']);
    Route::post('addresses', [AddressController::class, 'store']);
    Route::get('addresses/default', [AddressController::class, 'getDefault']);
    Route::get('addresses/{address}', [AddressController::class, 'show']);
    Route::put('addresses/{address}', [AddressController::class, 'update']);
    Route::delete('addresses/{address}', [AddressController::class, 'destroy']);
    Route::post('addresses/{address}/set-default', [AddressController::class, 'setDefault']);

    // Reviews routes
    Route::get('reviews', [ReviewController::class, 'myReviews']);
    Route::post('items/{item}/reviews', [ReviewController::class, 'store']);
    Route::put('reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);

    // Coupon validation
    Route::post('coupons/validate', [CouponController::class, 'validate']);

    // User - Notifications (FCM token auto-managed via login/register/logout)
    Route::get('notifications', [UserNotificationController::class, 'index']);
    Route::get('notifications/unread-count', [UserNotificationController::class, 'unreadCount']);
    Route::post('notifications/{id}/read', [UserNotificationController::class, 'markAsRead']);
    Route::post('notifications/mark-all-read', [UserNotificationController::class, 'markAllAsRead']);
});
