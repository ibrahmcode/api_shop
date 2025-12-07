<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminSettingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $group = $request->query('group');

        $settings = $group 
            ? Setting::where('group', $group)->get()
            : Setting::all();

        $grouped = $settings->groupBy('group')->map(function ($items) {
            return $items->map(function ($setting) {
                return [
                    'id' => $setting->id,
                    'key' => $setting->key,
                    'value' => $setting->getFormattedValue(),
                    'type' => $setting->type,
                    'description' => $setting->description,
                ];
            });
        });

        return response()->json([
            'success' => true,
            'data' => $grouped
        ]);
    }

    public function show(string $key): JsonResponse
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $setting->id,
                'key' => $setting->key,
                'value' => $setting->getFormattedValue(),
                'type' => $setting->type,
                'group' => $setting->group,
                'description' => $setting->description,
            ]
        ]);
    }

    public function update(Request $request, string $key): JsonResponse
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'value' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $value = $request->input('value');

        // Handle image uploads
        if ($setting->type === 'image' && $request->hasFile('value')) {
            // Delete old image
            if ($setting->value) {
                Storage::disk('public')->delete($setting->value);
            }

            $image = $request->file('value');
            $path = $image->store('settings', 'public');
            $value = $path;
        }

        // Handle JSON values
        if ($setting->type === 'json' && is_array($value)) {
            $value = json_encode($value);
        }

        Setting::set($key, $value);

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
            'data' => [
                'key' => $key,
                'value' => $setting->fresh()->getFormattedValue()
            ]
        ]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->settings as $item) {
            $setting = Setting::where('key', $item['key'])->first();
            if ($setting) {
                $value = $item['value'];
                
                if ($setting->type === 'json' && is_array($value)) {
                    $value = json_encode($value);
                }
                
                Setting::set($item['key'], $value);
            }
        }

        Setting::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }

    public function groups(): JsonResponse
    {
        $groups = Setting::select('group')
            ->distinct()
            ->pluck('group');

        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
    }

    public function clearCache(): JsonResponse
    {
        Setting::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully'
        ]);
    }
}
