<?php

namespace App\Services;

use App\Models\User;
use App\Traits\FCMNotificationTrait;
use Illuminate\Support\Facades\Log;

class FCMNotificationService
{
    use FCMNotificationTrait;

    /**
     * Send notification to specific users
     *
     * @param array $userIds Array of user IDs
     * @param string|array $title Notification title (string or multilingual array)
     * @param string|array $body Notification body (string or multilingual array)
     * @param array $data Additional data
     * @param string|null $modelType Model type for FCM data
     * @param int|null $modelId Model ID for FCM data
     * @return array Results
     */
    public function sendToUsers(array $userIds, $title, $body, array $data = [], ?string $modelType = null, ?int $modelId = null): array
    {
        $messageData = [
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ];

        return $this->sendFCMToUsers($userIds, $messageData, $modelType, $modelId);
    }

    /**
     * Send notification to a single user
     *
     * @param int $userId User ID
     * @param string|array $title Notification title (string or multilingual array)
     * @param string|array $body Notification body (string or multilingual array)
     * @param array $data Additional data
     * @param string|null $modelType Model type for FCM data
     * @param int|null $modelId Model ID for FCM data
     * @return array Result
     */
    public function sendToUser(int $userId, $title, $body, array $data = [], ?string $modelType = null, ?int $modelId = null): array
    {
        $messageData = [
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ];

        return $this->sendFCMToUser($userId, $messageData, $modelType, $modelId);
    }

    /**
     * Send notification to all active users
     *
     * @param string|array $title Notification title (string or multilingual array)
     * @param string|array $body Notification body (string or multilingual array)
     * @param array $data Additional data
     * @param string|null $modelType Model type for FCM data
     * @param int|null $modelId Model ID for FCM data
     * @return array Results
     */
    public function sendToAllUsers($title, $body, array $data = [], ?string $modelType = null, ?int $modelId = null): array
    {
        $users = User::whereHas('devices', function ($query) {
            $query->ofActive()->withFcmToken();
        })->get();

        if ($users->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No users with active devices found'
            ];
        }

        $messageData = [
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ];

        return $this->sendFCMToUsers($users->pluck('id')->toArray(), $messageData, $modelType, $modelId);
    }

    /**
     * Send notification to users by device type
     *
     * @param string $deviceType Device type (android, ios)
     * @param string|array $title Notification title (string or multilingual array)
     * @param string|array $body Notification body (string or multilingual array)
     * @param array $data Additional data
     * @param string|null $modelType Model type for FCM data
     * @param int|null $modelId Model ID for FCM data
     * @return array Results
     */
    public function sendToUsersByDeviceType(string $deviceType, $title, $body, array $data = [], ?string $modelType = null, ?int $modelId = null): array
    {
        $users = User::whereHas('devices', function ($query) use ($deviceType) {
            $query->ofActive()->withFcmToken()->ofDeviceType($deviceType);
        })->get();

        if ($users->isEmpty()) {
            return [
                'success' => false,
                'message' => "No users with active {$deviceType} devices found"
            ];
        }

        $messageData = [
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ];

        return $this->sendFCMToUsers($users->pluck('id')->toArray(), $messageData, $modelType, $modelId);
    }

    /**
     * Send notification to a topic
     *
     * @param string $topic Topic name
     * @param string|array $title Notification title (string or multilingual array)
     * @param string|array $body Notification body (string or multilingual array)
     * @param array $data Additional data
     * @param string|null $modelType Model type for FCM data
     * @param int|null $modelId Model ID for FCM data
     * @return array Result
     */
    public function sendToTopic(string $topic, $title, $body, array $data = [], ?string $modelType = null, ?int $modelId = null): array
    {
        $messageData = [
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ];

        return $this->sendFCMToTopic($topic, $messageData, $modelType, $modelId);
    }

    /**
     * Send consultation notification
     *
     * @param int $userId User ID
     * @param string|array $consultationTitle Consultation title (string or multilingual array)
     * @param string|array $message Message content (string or multilingual array)
     * @param int|null $consultationId Consultation ID
     * @return array Result
     */
    public function sendConsultationNotification(int $userId, $consultationTitle, $message, ?int $consultationId = null): array
    {
        $messageData = [
            'title' => [
                'en' => 'New Consultation Reply',
                'ar' => 'رد جديد على الاستشارة'
            ],
            'body' => $consultationTitle,
            'data' => [
                'type' => 'consultation',
                'consultation_title' => $consultationTitle,
                'message' => $message,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ];

        return $this->sendFCMToUser($userId, $messageData, 'consultation', $consultationId);
    }

    /**
     * Send appointment notification
     *
     * @param int $userId User ID
     * @param string|array $appointmentDate Appointment date (string or multilingual array)
     * @param string|array $serviceName Service name (string or multilingual array)
     * @param int|null $appointmentId Appointment ID
     * @return array Result
     */
    public function sendAppointmentNotification(int $userId, $appointmentDate, $serviceName, ?int $appointmentId = null): array
    {
        $messageData = [
            'title' => [
                'en' => 'Appointment Reminder',
                'ar' => 'تذكير بالموعد'
            ],
            'body' => [
                'en' => "Your appointment for {$serviceName} is scheduled for {$appointmentDate}",
                'ar' => "موعدك لـ {$serviceName} مجدول في {$appointmentDate}"
            ],
            'data' => [
                'type' => 'appointment',
                'appointment_date' => $appointmentDate,
                'service_name' => $serviceName,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ];

        return $this->sendFCMToUser($userId, $messageData, 'appointment', $appointmentId);
    }

    /**
     * Send promotional notification
     *
     * @param array $userIds Array of user IDs
     * @param string|array $title Promotional title (string or multilingual array)
     * @param string|array $body Promotional body (string or multilingual array)
     * @param string $imageUrl Image URL
     * @param string $actionUrl Action URL
     * @param int|null $promotionId Promotion ID
     * @return array Results
     */
    public function sendPromotionalNotification(array $userIds, $title, $body, string $imageUrl = '', string $actionUrl = '', ?int $promotionId = null): array
    {
        $messageData = [
            'title' => $title,
            'body' => $body,
            'image' => $imageUrl,
            'data' => [
                'type' => 'promotional',
                'action_url' => $actionUrl,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ];

        return $this->sendFCMToUsers($userIds, $messageData, 'promotion', $promotionId);
    }

    /**
     * Send notification to guest devices (non-authenticated users)
     *
     * @param array $fcmTokens Array of FCM tokens
     * @param string|array $title Notification title (string or multilingual array)
     * @param string|array $body Notification body (string or multilingual array)
     * @param array $data Additional data
     * @param string|null $modelType Model type for FCM data
     * @param int|null $modelId Model ID for FCM data
     * @return array Results
     */
    public function sendToGuests(array $fcmTokens, $title, $body, array $data = [], ?string $modelType = null, ?int $modelId = null): array
    {
        $messageData = [
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ];

        return $this->sendFCMToGuests($fcmTokens, $messageData, $modelType, $modelId);
    }

    /**
     * Send notification to all devices (authenticated + guest)
     *
     * @param string|array $title Notification title (string or multilingual array)
     * @param string|array $body Notification body (string or multilingual array)
     * @param array $data Additional data
     * @param string|null $modelType Model type for FCM data
     * @param int|null $modelId Model ID for FCM data
     * @return array Results
     */
    public function sendToAllDevices($title, $body, array $data = [], ?string $modelType = null, ?int $modelId = null): array
    {
        $messageData = [
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ];

        return $this->sendFCMToAllDevices($messageData, $modelType, $modelId);
    }

    /**
     * Get notification statistics
     *
     * @return array Statistics
     */
    public function getNotificationStats(): array
    {
        $totalUsers = User::count();
        $usersWithDevices = User::whereHas('devices')->count();
        $usersWithActiveDevices = User::whereHas('devices', function ($query) {
            $query->ofActive()->withFcmToken();
        })->count();

        $androidDevices = User::whereHas('devices', function ($query) {
            $query->ofActive()->ofAndroid()->withFcmToken();
        })->count();

        $iosDevices = User::whereHas('devices', function ($query) {
            $query->ofActive()->ofIos()->withFcmToken();
        })->count();

        // Guest devices
        $guestDevices = \App\Models\Device::whereNull('deviceable_type')
            ->ofActive()
            ->withFcmToken()
            ->count();

        return [
            'total_users' => $totalUsers,
            'users_with_devices' => $usersWithDevices,
            'users_with_active_devices' => $usersWithActiveDevices,
            'android_devices' => $androidDevices,
            'ios_devices' => $iosDevices,
            'guest_devices' => $guestDevices,
            'total_active_devices' => $usersWithActiveDevices + $guestDevices,
            'coverage_percentage' => $totalUsers > 0 ? round(($usersWithActiveDevices / $totalUsers) * 100, 2) : 0,
        ];
    }
}
