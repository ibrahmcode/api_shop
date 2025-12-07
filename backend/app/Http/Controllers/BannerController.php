<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    public function index(): JsonResponse
    {
        $banners = Banner::active()
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $banners
        ]);
    }

    public function show(Banner $banner): JsonResponse
    {
        if (!$banner->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $banner
        ]);
    }
}
