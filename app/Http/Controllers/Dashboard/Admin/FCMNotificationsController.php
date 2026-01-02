<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FCMNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FCMNotificationsController extends Controller
{
    protected $fcmService;

    public function __construct()
    {
 
        $this->fcmService = new FCMNotificationService();
        $this->middleware('permission:fcm-setting')->only(['index']);
    }

    /**
     * Display the notifications page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stats = $this->fcmService->getNotificationStats();
        return view('dashboard.admin.fcm-notifications.index', compact('stats'));
    }

    /**
     * Send notification to authenticated users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendToAuthenticatedUsers(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'body_ar'  => 'required|string|max:1000',
            'body_en'  => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $title = [
                'ar' => $request->title_ar,
                'en' => $request->title_en,
            ];
            $body  = [
                'ar' => $request->body_ar,
                'en' => $request->body_en,
            ];

            // Send notifications to all authenticated users
            $results = $this->fcmService->sendToAllUsers($title, $body);

            return response()->json([
                'success' => true,
                'message' => "تم إرسال الإشعارات للمستخدمين المسجلين بنجاح",
                'data'    => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إرسال الإشعارات',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to guest users (unauthenticated)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendToGuestUsers(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'body_ar'  => 'required|string|max:1000',
            'body_en'  => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $title = [
                'ar' => $request->title_ar,
                'en' => $request->title_en,
            ];
            $body  = [
                'ar' => $request->body_ar,
                'en' => $request->body_en,
            ];

            // Get all guest devices (devices without user association)
            $guestDevices = \App\Models\Device::whereNull('deviceable_type')
                ->ofActive()
                ->withFcmToken()
                ->get();

            if ($guestDevices->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد أجهزة ضيوف نشطة'
                ], 400);
            }

            $fcmTokens = $guestDevices->pluck('fcm_token')->toArray();
            $results   = $this->fcmService->sendToGuests($fcmTokens, $title, $body);

            return response()->json([
                'success' => true,
                'message' => "تم إرسال الإشعارات للضيوف بنجاح",
                'data'    => [
                    'guest_devices_count' => $guestDevices->count(),
                    'results'             => $results
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إرسال الإشعارات للضيوف',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to all devices (authenticated + guest)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendToAllDevices(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'body_ar'  => 'required|string|max:1000',
            'body_en'  => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $title = [
                'ar' => $request->title_ar,
                'en' => $request->title_en,
            ];
            $body  = [
                'ar' => $request->body_ar,
                'en' => $request->body_en,
            ];

            // Send notifications to all devices
            $results = $this->fcmService->sendToAllDevices($title, $body);

            return response()->json([
                'success' => true,
                'message' => "تم إرسال الإشعارات لجميع الأجهزة بنجاح",
                'data'    => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إرسال الإشعارات لجميع الأجهزة',
                'error'   => $e->getMessage()
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
            'title_ar'    => 'required|string|max:255',
            'title_en'    => 'required|string|max:255',
            'body_ar'     => 'required|string|max:1000',
            'body_en'     => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $deviceType = $request->device_type;
            $title      = [
                'ar' => $request->title_ar,
                'en' => $request->title_en,
            ];
            $body       = [
                'ar' => $request->body_ar,
                'en' => $request->body_en,
            ];

            // Send notifications by device type
            $results = $this->fcmService->sendToUsersByDeviceType($deviceType, $title, $body);

            return response()->json([
                'success' => true,
                'message' => "تم إرسال الإشعارات لمستخدمي {$deviceType} بنجاح",
                'data'    => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إرسال الإشعارات حسب نوع الجهاز',
                'error'   => $e->getMessage()
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
                'data'    => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في الحصول على إحصائيات الإشعارات',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
