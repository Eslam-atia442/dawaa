<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserNotificationResource;
use App\Traits\BaseApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Api
 * @subgroup Notifications
 */
class NotificationController extends Controller
{
    use BaseApiResponseTrait;

    /**
     * Get authenticated user's notifications.
     * @authenticated
     * @queryParam limit integer Items per page (default: 15)
     * @queryParam page integer Page number
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        $limit = min((int) ($request->limit ?? 15), 50);
        $page = (int) ($request->page ?? 1);

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        $unreadCount = $user->unreadNotifications()->count();

        return $this->respondWithSuccess(__('trans.notifications_retrieved_successfully'), [
            'notifications' => UserNotificationResource::collection($notifications),
            'unread_count' => $unreadCount,
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Get unread notifications count.
     * @authenticated
     * @return JsonResponse
     */
    public function unreadCount(): JsonResponse
    {
        $user = auth('sanctum')->user();
        $count = $user->unreadNotifications()->count();

        return $this->respondWithSuccess(__('trans.success'), [
            'unread_count' => $count,
        ]);
    }

    /**
     * Mark a notification as read.
     * @authenticated
     * @urlParam notification string required Notification UUID
     * @return JsonResponse
     */
    public function markAsRead(string $id): JsonResponse
    {
        $user = auth('sanctum')->user();
        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            return $this->respondWithError(__('trans.notification_not_found'), 404);
        }

        $notification->markAsRead();

        return $this->respondWithSuccess(__('trans.notification_marked_as_read'));
    }

    /**
     * Mark all notifications as read.
     * @authenticated
     * @return JsonResponse
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = auth('sanctum')->user();
        $user->unreadNotifications->markAsRead();

        return $this->respondWithSuccess(__('trans.all_notifications_marked_as_read'));
    }

    /**
     * Get a single notification by ID.
     * @authenticated
     * @urlParam notification string required Notification UUID
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $user = auth('sanctum')->user();
        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            return $this->respondWithError(__('trans.notification_not_found'), 404);
        }

        return $this->respondWithSuccess(__('trans.success'), [
            'notification' => new UserNotificationResource($notification),
        ]);
    }
}
