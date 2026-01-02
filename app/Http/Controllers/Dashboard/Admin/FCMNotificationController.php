<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FCMNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FCMNotificationController extends Controller
{
    protected $fcmService;

    public function __construct()
    {
        $this->fcmService = new FCMNotificationService();
    }

    /**
     * Send notification to selected users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendToUsers(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'body_ar' => 'required|string|max:1000',
            'body_en' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $title = [
                'ar' => $request->title_ar,
                'en' => $request->title_en,
            ];
            $body = [
                'ar' => $request->body_ar,
                'en' => $request->body_en,
            ];

            // Send notifications to selected users
            $results = $this->fcmService->sendToUsers($request->user_ids, $title, $body);

            return response()->json([
                'success' => true,
                'message' => "تم إرسال الإشعارات للمستخدمين المحددين بنجاح",
                'data' => [
                    'total_users_selected' => count($request->user_ids),
                    'users_with_devices' => count($results),
                    'notifications_sent' => count(array_filter($results, fn($r) => $r['success'])),
                    'notifications_failed' => count(array_filter($results, fn($r) => !$r['success'])),
                    'results' => $results
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إرسال الإشعارات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to all users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendToAllUsers(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'body_ar' => 'required|string|max:1000',
            'body_en' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $title = [
                'ar' => $request->title_ar,
                'en' => $request->title_en,
            ];
            $body = [
                'ar' => $request->body_ar,
                'en' => $request->body_en,
            ];

            // Send notifications to all users
            $results = $this->fcmService->sendToAllUsers($title, $body);

            return response()->json([
                'success' => true,
                'message' => "تم إرسال الإشعارات لجميع المستخدمين بنجاح",
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إرسال الإشعارات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification by device type
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendByDeviceType(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_type' => 'required|in:android,ios,web',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'body_ar' => 'required|string|max:1000',
            'body_en' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $deviceType = $request->device_type;
            $title = [
                'ar' => $request->title_ar,
                'en' => $request->title_en,
            ];
            $body = [
                'ar' => $request->body_ar,
                'en' => $request->body_en,
            ];

            // Send notifications by device type
            $results = $this->fcmService->sendToUsersByDeviceType($deviceType, $title, $body);

            return response()->json([
                'success' => true,
                'message' => "تم إرسال الإشعارات لمستخدمي {$deviceType} بنجاح",
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إرسال الإشعارات حسب نوع الجهاز',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notification statistics
     *
     * @return JsonResponse
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = $this->fcmService->getNotificationStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في الحصول على إحصائيات الإشعارات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users with devices
     *
     * @return JsonResponse
     */
    public function getUsersWithDevices(): JsonResponse
    {
        try {
            $users = User::whereHas('devices', function ($query) {
                $query->ofActive()->withFcmToken();
            })->select('id', 'name')->get();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في الحصول على المستخدمين',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}