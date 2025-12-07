<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Send notification to a specific user.
     */
    public function sendToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'nullable|string',
            'data' => 'nullable|array',
        ]);

        $result = $this->firebaseService->sendToUser(
            $request->user_id,
            $request->title,
            $request->body,
            $request->data ?? [],
            $request->type ?? 'general'
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Send notification to all users.
     */
    public function sendToAll(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'nullable|string',
            'data' => 'nullable|array',
        ]);

        $result = $this->firebaseService->sendToAllUsers(
            $request->title,
            $request->body,
            $request->data ?? [],
            $request->type ?? 'general'
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get all notifications.
     */
    public function index()
    {
        $notifications = Notification::with('user')
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ], 200);
    }

    /**
     * Get all users for notification targeting.
     */
    public function getUsers()
    {
        $users = User::where('role', 'user')
                    ->select('id', 'name', 'email')
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ], 200);
    }
}
