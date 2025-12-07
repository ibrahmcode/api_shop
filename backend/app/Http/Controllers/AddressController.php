<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function index(): JsonResponse
    {
        $addresses = auth()->user()->addresses()->get();

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'label' => 'nullable|string|in:home,work,other',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'area' => 'nullable|string|max:100',
            'street_address' => 'required|string',
            'additional_info' => 'nullable|string',
            'postal_code' => 'nullable|string|max:20',
            'is_default' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = auth()->id();

        // If this is the first address, make it default
        if (!auth()->user()->addresses()->exists()) {
            $data['is_default'] = true;
        }

        $address = Address::create($data);

        // If marked as default, unset others
        if ($address->is_default) {
            $address->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'message' => 'Address created successfully',
            'data' => $address
        ], 201);
    }

    public function show(Address $address): JsonResponse
    {
        if ($address->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $address
        ]);
    }

    public function update(Request $request, Address $address): JsonResponse
    {
        if ($address->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'label' => 'nullable|string|in:home,work,other',
            'recipient_name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'city' => 'sometimes|required|string|max:100',
            'area' => 'nullable|string|max:100',
            'street_address' => 'sometimes|required|string',
            'additional_info' => 'nullable|string',
            'postal_code' => 'nullable|string|max:20',
            'is_default' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $address->update($validator->validated());

        // If marked as default, unset others
        if ($request->has('is_default') && $request->is_default) {
            $address->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data' => $address->fresh()
        ]);
    }

    public function destroy(Address $address): JsonResponse
    {
        if ($address->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // If deleting default address, set another as default
        if ($address->is_default) {
            $nextAddress = Address::where('user_id', auth()->id())
                ->where('id', '!=', $address->id)
                ->first();
            
            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
            }
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    public function setDefault(Address $address): JsonResponse
    {
        if ($address->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $address->setAsDefault();

        return response()->json([
            'success' => true,
            'message' => 'Default address updated successfully',
            'data' => $address
        ]);
    }

    public function getDefault(): JsonResponse
    {
        $address = auth()->user()->addresses()->where('is_default', true)->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'No default address found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $address
        ]);
    }
}
