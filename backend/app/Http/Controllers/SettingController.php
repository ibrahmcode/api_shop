<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $group = $request->query('group');

        $settings = $group 
            ? Setting::getByGroup($group)
            : Setting::getAllSettings();

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    public function get(string $key): JsonResponse
    {
        $value = Setting::get($key);

        if ($value === null) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'key' => $key,
                'value' => $value
            ]
        ]);
    }

    public function appConfig(): JsonResponse
    {
        $config = [
            'app' => [
                'name' => Setting::get('app_name'),
                'logo' => Setting::get('app_logo'),
                'currency' => Setting::get('currency'),
                'language' => Setting::get('default_language', 'ku'),
                'supported_languages' => Setting::get('supported_languages', ['ku', 'ar', 'en']),
            ],
            'colors' => [
                'primary' => Setting::get('primary_color'),
                'secondary' => Setting::get('secondary_color'),
                'background' => Setting::get('background_color'),
                'text' => Setting::get('text_color'),
                'success' => Setting::get('success_color'),
                'error' => Setting::get('error_color'),
                'warning' => Setting::get('warning_color'),
            ],
            'contact' => [
                'phone' => Setting::get('contact_phone'),
                'email' => Setting::get('contact_email'),
                'address' => Setting::get('contact_address'),
                'whatsapp' => Setting::get('contact_whatsapp'),
            ],
            'social' => [
                'facebook' => Setting::get('facebook_url'),
                'instagram' => Setting::get('instagram_url'),
                'twitter' => Setting::get('twitter_url'),
                'youtube' => Setting::get('youtube_url'),
            ],
            'shipping' => [
                'fee' => (float) Setting::get('shipping_fee', 0),
                'free_above' => (float) Setting::get('free_shipping_above', 0),
                'tax_rate' => (float) Setting::get('tax_rate', 0),
            ],
            'features' => [
                'reviews_enabled' => (bool) Setting::get('enable_reviews', true),
                'coupons_enabled' => (bool) Setting::get('enable_coupons', true),
                'featured_items_count' => (int) Setting::get('featured_items_count', 10),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $config
        ]);
    }

    public function content(string $page): JsonResponse
    {
        $validPages = ['about_us', 'terms_conditions', 'privacy_policy', 'return_policy'];

        if (!in_array($page, $validPages)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid page'
            ], 404);
        }

        $content = Setting::get($page);

        return response()->json([
            'success' => true,
            'data' => [
                'page' => $page,
                'content' => $content
            ]
        ]);
    }

    public function languages(): JsonResponse
    {
        $languages = [
            [
                'code' => 'ku',
                'name' => 'کوردی',
                'name_en' => 'Kurdish',
                'is_default' => true,
                'rtl' => true
            ],
            [
                'code' => 'ar',
                'name' => 'عربي',
                'name_en' => 'Arabic',
                'is_default' => false,
                'rtl' => true
            ],
            [
                'code' => 'en',
                'name' => 'English',
                'name_en' => 'English',
                'is_default' => false,
                'rtl' => false
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $languages
        ]);
    }
}
