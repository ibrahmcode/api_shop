<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserNotificationController extends Controller
{
    /**
     * Store/Update user's FCM token.
     */
    public function storeToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|in:android,ios,web',
        ]);

        $user = Auth::user();

        // Check if token already exists
        $fcmToken = FcmToken::where('token', $request->token)->first();

        if ($fcmToken) {
            // Update existing token
            $fcmToken->update([
                'user_id' => $user->id,
                'device_type' => $request->device_type ?? 'android',
            ]);
        } else {
            // Create new token
            $fcmToken = FcmToken::create([
                'user_id' => $user->id,
                'token' => $request->token,
                'device_type' => $request->device_type ?? 'android',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'FCM token stored successfully',
            'data' => $fcmToken
        ], 200);
    }

    /**
     * Delete user's FCM token (logout from notifications).
     */
    public function deleteToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = Auth::user();

        FcmToken::where('user_id', $user->id)
                ->where('token', $request->token)
                ->delete();

        return response()->json([
            'success' => true,
            'message' => 'FCM token deleted successfully'
        ], 200);
    }

    /**
     * Get user's notifications.
     */
    public function index()
    {
        $user = Auth::user();

        $notifications = Notification::where('user_id', $user->id)
                                    ->orWhereNull('user_id') // Include broadcast notifications
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ], 200);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead($id)
    {
        $user = Auth::user();

        $notification = Notification::where('id', $id)
                                    ->where(function($query) use ($user) {
                                        $query->where('user_id', $user->id)
                                              ->orWhereNull('user_id');
                                    })
                                    ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'data' => $notification
        ], 200);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        Notification::where('user_id', $user->id)
                    ->where('is_read', false)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ], 200);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount()
    {
        $user = Auth::user();

        $count = Notification::where(function($query) use ($user) {
                                $query->where('user_id', $user->id)
                                      ->orWhereNull('user_id');
                            })
                            ->where('is_read', false)
                            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ], 200);
    }
}
