<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use App\Traits\FCMNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Api
 * @subgroup Device
 */
class DeviceController extends Controller
{
    use FCMNotificationTrait;

    /**
     *  FCM token management endpoint
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function manageFCMToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fcm_token'   => 'required|string',
            'device_type' => 'required_if:action,store,update|in:android,ios,web',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $fcmToken = $request->fcm_token;
            $user = auth('sanctum')->user();


            return $this->storeOrUpdateToken($request, $user, $fcmToken);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to manage FCM token',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update FCM token
     *
     * @param Request $request
     * @param mixed $user
     * @param string $fcmToken
     * @return JsonResponse
     */
    private function storeOrUpdateToken(Request $request, $user, string $fcmToken): JsonResponse
    {
        // Check if device with this FCM token already exists
        $existingDevice = Device::where('fcm_token', $fcmToken)->first();

        $deviceData = [
            'device_type'  => $request->device_type,
            'is_active'    => true,
            'last_used_at' => now(),
        ];

        if ($user) {
            // Authenticated user
            $deviceData['deviceable_type'] = 'User';
            $deviceData['deviceable_id'] = $user->id;
            $message = 'FCM token stored successfully for authenticated user';
        } else {
            // Guest user
            $deviceData['deviceable_type'] = null;
            $deviceData['deviceable_id'] = null;
            $message = 'FCM token stored successfully for guest user';
        }

        if ($existingDevice) {
            // Update existing device
            $existingDevice->update($deviceData);
            $message = str_replace('stored', 'updated', $message);

            // Log token update
            $this->logFCMToken('updated', $fcmToken, [
                'device_id'   => $existingDevice->id,
                'device_type' => $request->device_type,
                'user_id'     => $user?->id,
                'is_guest'    => $user === null,
            ]);

            return response()->json([
                'success'   => true,
                'message'   => $message,
                'device'    => $existingDevice,
                'user_type' => $user ? 'authenticated' : 'guest'
            ]);
        } else {
            // Create new device
            $deviceData['fcm_token'] = $fcmToken;
            $device = Device::create($deviceData);

            // Log token creation
            $this->logFCMToken('stored', $fcmToken, [
                'device_id'   => $device->id,
                'device_type' => $request->device_type,
                'user_id'     => $user?->id,
                'is_guest'    => $user === null,
            ]);

            return response()->json([
                'success'   => true,
                'message'   => $message,
                'device'    => $device,
                'user_type' => $user ? 'authenticated' : 'guest'
            ], 201);
        }
    }

    /**
     * Remove FCM token
     *
     * @param string $fcmToken
     * @return JsonResponse
     */
    private function removeToken(string $fcmToken): JsonResponse
    {
        $device = Device::where('fcm_token', $fcmToken)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'FCM token not found'
            ], 404);
        }

        // Deactivate the device instead of deleting
        $device->update([
            'is_active' => false,
            'fcm_token' => null, // Clear the token
        ]);

        // Log token removal
        $this->logFCMToken('removed', $fcmToken, [
            'device_id'   => $device->id,
            'device_type' => $device->device_type,
            'user_id'     => $device->deviceable_id,
            'is_guest'    => $device->deviceable_type === null,
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'FCM token removed successfully',
            'user_type' => $device->deviceable_type ? 'authenticated' : 'guest'
        ]);
    }

    /**
     * Get user's devices (authenticated users only)
     *
     * @return JsonResponse
     */
    public function getUserDevices(): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $devices = $user->devices()->ofActive()->get();

            return response()->json([
                'success' => true,
                'devices' => $devices
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get devices',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update device last used timestamp
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateLastUsed(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $device = Device::where('fcm_token', $request->fcm_token)->first();

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found'
                ], 404);
            }

            $device->markAsUsed();

            return response()->json([
                'success' => true,
                'message' => 'Last used timestamp updated'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update timestamp',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get device statistics
     *
     * @return JsonResponse
     */
    public function getDeviceStats(): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();

            $stats = [
                'total_devices'    => Device::count(),
                'active_devices'   => Device::ofActive()->count(),
                'devices_with_fcm' => Device::ofActive()->withFcmToken()->count(),
                'android_devices'  => Device::ofActive()->ofAndroid()->count(),
                'ios_devices'      => Device::ofActive()->ofIos()->count(),
                'web_devices'      => Device::ofActive()->ofDeviceType('web')->count(),
            ];

            if ($user) {
                $stats['user_devices'] = $user->devices()->ofActive()->count();
                $stats['user_devices_with_fcm'] = $user->devices()->ofActive()->withFcmToken()->count();
            }

            return response()->json([
                'success' => true,
                'stats'   => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get device statistics',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
