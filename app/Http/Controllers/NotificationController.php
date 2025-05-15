<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    public function sendNotifications(Request $request)
    {
        $request->validate([
            'type' => 'required|in:issue,support,new_order,new_system_action',
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $users = User::all();

        $notification = new GeneralNotification(
            $request->title,
            $request->content,
            ['database'],
            $request->type
        );

        Notification::send($users, $notification);

        return response()->json([
            'success' => true,
            'message' => 'Notification Sent Successfully',
        ], 200);
    }

    public function getUserNotification()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'data' => [],
            ], 401);
        }

        $notifications = $user->notifications()->latest()->paginate(5);

        $data = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? '',
                'content' => $notification->data['content'] ?? '',
                'type' => $notification->data['type'] ?? 'general',
                'created_at' => Carbon::parse($notification->created_at)->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Notifications fetched successfully',
            'data' => ['notifications' => $data],
        ]);
    }

    public function getUserUnreadNotifications()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'data' => [],
            ], 401);
        }

        $notifications = $user->unreadNotifications()->latest()->paginate(5);

        $data = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? '',
                'content' => $notification->data['content'] ?? '',
                'type' => $notification->data['type'] ?? 'general',
                'created_at' => Carbon::parse($notification->created_at)->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Unread notifications fetched successfully',
            'data' => ['notifications' => $data],
        ]);
    }

    public function getUserReadNotifications()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'data' => [],
            ], 401);
        }

        $notifications = $user->readNotifications()->latest()->paginate(5);

        $data = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? '',
                'content' => $notification->data['content'] ?? '',
                'type' => $notification->data['type'] ?? 'general',
                'created_at' => Carbon::parse($notification->created_at)->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Read notifications fetched successfully',
            'data' => ['notifications' => $data],
        ]);
    }

    public function markNotificationAsRead($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'data' => [],
            ], 401);
        }

        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
                'data' => [],
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }
}

