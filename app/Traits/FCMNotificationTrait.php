<?php

namespace App\Traits;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Exception;
use Illuminate\Support\Facades\Log;

trait FCMNotificationTrait
{
    /**
     * Send FCM notification to users
     *
     * @param array $users Array of User models or user IDs
     * @param array $messageData Notification data
     * @param string $type Type of notification (single, multiple, topic)
     * @param string|null $modelType Model type for FCM data (e.g., 'consultation', 'appointment')
     * @param int|null $modelId Model ID for FCM data
     * @return array Results of the notification sending
     */
    public function sendFCM(array $users, array $messageData, string $type = 'multiple', ?string $modelType = null, ?int $modelId = null): array
    {
        $results = [];

        try {
            $messaging = $this->getFirebaseMessaging();

            foreach ($users as $user) {

                $userModel = is_numeric($user) ? \App\Models\User::find($user) : $user;


                if (!$userModel || !$userModel->devices()->exists()) {
                    $results[] = [
                        'user_id' => $userModel?->id,
                        'success' => false,
                        'message' => 'User not found or has no devices'
                    ];
                    continue;
                }

                $devices = $userModel->devices()->ofActive()->whereNotNull('fcm_token')->get();

                if ($devices->isEmpty()) {
                    $results[] = [
                        'user_id' => $userModel->id,
                        'success' => false,
                        'message' => 'No FCM tokens found for user'
                    ];
                    continue;
                }

                $tokens = $devices->pluck('fcm_token')->toArray();
                $deviceTypes = $devices->pluck('device_type')->toArray();

                $result = $this->sendToDevices($messaging, $tokens, $deviceTypes, $messageData, $type, $modelType, $modelId);

                // Log the result
                if ($result['success']) {
                    $this->logFCMSuccess(
                        $tokens,
                        $messageData['title'] ?? '',
                        $messageData['body'] ?? '',
                        [
                            'user_id' => $userModel->id,
                            'devices_count' => $devices->count(),
                            'device_types' => $deviceTypes,
                            'type' => $type,
                        ]
                    );
                } else {
                    $this->logFCMFailure(
                        $tokens,
                        $result['message'],
                        $messageData['title'] ?? '',
                        $messageData['body'] ?? '',
                        [
                            'user_id' => $userModel->id,
                            'devices_count' => $devices->count(),
                            'device_types' => $deviceTypes,
                            'type' => $type,
                        ]
                    );
                }

                $results[] = [
                    'user_id' => $userModel->id,
                    'success' => $result['success'],
                    'message' => $result['message'],
                    'tokens_sent' => count($tokens)
                ];
            }

        } catch (Exception $e) {
            Log::error('FCM Notification Error: ' . $e->getMessage());
            $results[] = [
                'success' => false,
                'message' => 'FCM service error: ' . $e->getMessage()
            ];
        }

        return $results;
    }

    /**
     * Send FCM notification to a single user
     *
     * @param mixed $user User model or user ID
     * @param array $messageData Notification data
     * @param string|null $modelType Model type for FCM data
     * @param int|null $modelId Model ID for FCM data
     * @return array Result of the notification sending
     */
    public function sendFCMToUser($user, array $messageData, ?string $modelType = null, ?int $modelId = null): array
    {
        return $this->sendFCM([$user], $messageData, 'single', $modelType, $modelId);
    }

    /**
     * Send FCM notification to multiple users
     *
     * @param array $users Array of User models or user IDs
     * @param array $messageData Notification data
     * @param string|null $modelType Model type for FCM data
     * @param int|null $modelId Model ID for FCM data
     * @return array Results of the notification sending
     */
    public function sendFCMToUsers(array $users, array $messageData, ?string $modelType = null, ?int $modelId = null): array
    {
        return $this->sendFCM($users, $messageData, 'multiple', $modelType, $modelId);
    }

    /**
     * Send FCM notification to a topic
     *
     * @param string $topic Topic name
     * @param array $messageData Notification data
     * @param string|null $modelType Model type for FCM data
     * @param int|null $modelId Model ID for FCM data
     * @return array Result of the notification sending
     */
    public function sendFCMToTopic(string $topic, array $messageData, ?string $modelType = null, ?int $modelId = null): array
    {
        try {
            $messaging = $this->getFirebaseMessaging();
            $message = $this->buildMessage($messageData, null, 'topic', $topic, $modelType, $modelId);

            $report = $messaging->send($message);

            // Log successful topic notification
            $this->logFCMSuccess(
                [$topic], // Topic as "token"
                $messageData['title'] ?? '',
                $messageData['body'] ?? '',
                [
                    'topic' => $topic,
                    'type' => 'topic',
                ]
            );

            return [
                'success' => true,
                'message' => 'Notification sent to topic successfully',
                'topic' => $topic,
                'report' => $report
            ];

        } catch (Exception $e) {
            Log::error('FCM Topic Notification Error: ' . $e->getMessage());

            // Log failed topic notification
            $this->logFCMFailure(
                [$topic], // Topic as "token"
                $e->getMessage(),
                $messageData['title'] ?? '',
                $messageData['body'] ?? '',
                [
                    'topic' => $topic,
                    'type' => 'topic',
                ]
            );

            return [
                'success' => false,
                'message' => 'FCM topic notification error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send notification to specific devices
     *
     * @param mixed $messaging Firebase messaging instance
     * @param array $tokens Array of FCM tokens
     * @param array $deviceTypes Array of device types
     * @param array $messageData Notification data
     * @param string $type Type of sending
     * @param string|null $modelType Model type for FCM data
     * @param int|null $modelId Model ID for FCM data
     * @return array Result of the notification sending
     */
    protected function sendToDevices($messaging, array $tokens, array $deviceTypes, array $messageData, string $type, ?string $modelType = null, ?int $modelId = null): array
    {
        try {
            if (count($tokens) === 1) {
                // Single device
                $message = $this->buildMessage($messageData, $deviceTypes[0], 'token', $tokens[0], $modelType, $modelId);
                $report = $messaging->send($message);

                return [
                    'success' => true,
                    'message' => 'Notification sent successfully',
                    'report' => $report
                ];
            } else {
                // Multiple devices
                $androidTokens = [];
                $iosTokens = [];

                foreach ($tokens as $index => $token) {
                    if ($deviceTypes[$index] === 'android') {
                        $androidTokens[] = $token;
                    } elseif ($deviceTypes[$index] === 'ios') {
                        $iosTokens[] = $token;
                    }
                }

                $results = [];

                // Send to Android devices
                if (!empty($androidTokens)) {
                    $androidMessage = $this->buildMessage($messageData, 'android', 'tokens', $androidTokens, $modelType, $modelId);
                    $androidReport = $messaging->sendMulticast($androidMessage, $androidTokens);
                    $results['android'] = $androidReport;
                }

                // Send to iOS devices
                if (!empty($iosTokens)) {
                    $iosMessage = $this->buildMessage($messageData, 'ios', 'tokens', $iosTokens, $modelType, $modelId);
                    $iosReport = $messaging->sendMulticast($iosMessage, $iosTokens);
                    $results['ios'] = $iosReport;
                }

                return [
                    'success' => true,
                    'message' => 'Notifications sent successfully',
                    'results' => $results
                ];
            }

        } catch (Exception $e) {
            Log::error('FCM Device Notification Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'FCM device notification error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Build CloudMessage based on device type and target
     *
     * @param array $messageData Notification data
     * @param string|null $deviceType Device type (android, ios)
     * @param string $targetType Target type (token, tokens, topic)
     * @param mixed $target Target value (token, array of tokens, topic)
     * @param string|null $modelType Model type for FCM data
     * @param int|null $modelId Model ID for FCM data
     * @return CloudMessage
     */
    protected function buildMessage(array $messageData, ?string $deviceType, string $targetType, $target, ?string $modelType = null, ?int $modelId = null): CloudMessage
    {
        // Handle multilingual notifications - get localized text for notification display
        $title = $this->getLocalizedText($messageData['title'] ?? '');
        $body = $this->getLocalizedText($messageData['body'] ?? '');

        $notification = Notification::fromArray([
            'title' => $title,
            'body' => $body,
        ]);

        // Prepare FCM data with model information
        $fcmData = $messageData['data'] ?? [];

        // Add model information to FCM data
        if ($modelType !== null) {
            $fcmData['model_type'] = (string) $modelType;
        }
        if ($modelId !== null) {
            $fcmData['model_id'] = (string) $modelId;
        }

        // Add multilingual data to FCM data for client-side handling
        if (isset($messageData['title'])) {
            if (is_array($messageData['title'])) {
                // Add both Arabic and English versions
                $fcmData['title_ar'] = (string) ($messageData['title']['ar'] ?? '');
                $fcmData['title_en'] = (string) ($messageData['title']['en'] ?? '');
                $fcmData['title_data'] = json_encode($messageData['title']);
            } else {
                $fcmData['title_data'] = (string) $messageData['title'];
            }
        }

        if (isset($messageData['body'])) {
            if (is_array($messageData['body'])) {
                // Add both Arabic and English versions
                $fcmData['body_ar'] = (string) ($messageData['body']['ar'] ?? '');
                $fcmData['body_en'] = (string) ($messageData['body']['en'] ?? '');
                $fcmData['body_data'] = json_encode($messageData['body']);
            } else {
                $fcmData['body_data'] = (string) $messageData['body'];
            }
        }

        // Ensure all FCM data values are strings
        $fcmData = array_map(function($value) {
            if (is_array($value)) {
                return json_encode($value);
            }
            return (string) $value;
        }, $fcmData);

        $messageArray = [
            'notification' => $notification,
            'data' => $fcmData,
        ];

        // Set target
        if ($targetType === 'topic') {
            $messageArray['topic'] = $target;
        } elseif ($targetType === 'token') {
            $messageArray['token'] = $target;
        }

        // Add platform-specific configurations
        if ($deviceType === 'android') {
            $messageArray['android'] = $this->getAndroidConfig($messageData);
        } elseif ($deviceType === 'ios') {
            $messageArray['apns'] = $this->getApnsConfig($messageData);
        }

        return CloudMessage::fromArray($messageArray);
    }

    /**
     * Get Android configuration
     *
     * @param array $messageData Notification data
     * @return AndroidConfig
     */
    protected function getAndroidConfig(array $messageData): AndroidConfig
    {
        $config = [
            'priority' => 'high',
            'notification' => [
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]
        ];

        if (isset($messageData['channel_id'])) {
            $config['notification']['channel_id'] = $messageData['channel_id'];
        }

        if (isset($messageData['icon'])) {
            $config['notification']['icon'] = $messageData['icon'];
        }

        if (isset($messageData['color'])) {
            $config['notification']['color'] = $messageData['color'];
        }

        return AndroidConfig::fromArray($config);
    }

    /**
     * Get APNS (iOS) configuration
     *
     * @param array $messageData Notification data
     * @return ApnsConfig
     */
    protected function getApnsConfig(array $messageData): ApnsConfig
    {
        $apsData = [
            'sound' => 'default',
            'content-available' => 1,
        ];

        if (isset($messageData['badge'])) {
            $apsData['badge'] = $messageData['badge'];
        }

        $config = [
            'headers' => ['apns-priority' => '10'],
            'payload' => [
                'aps' => $apsData,
            ],
        ];

        return ApnsConfig::fromArray($config);
    }

    /**
     * Get Firebase messaging instance
     *
     * @return mixed Firebase messaging instance
     * @throws Exception
     */
    protected function getFirebaseMessaging()
    {
        $serviceAccountPath = config('services.firebase.service_account_path');

        if (!$serviceAccountPath || !file_exists($serviceAccountPath)) {
            throw new Exception('Firebase service account file not found');
        }

        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        return $factory->createMessaging();
    }

    /**
     * Validate message data
     *
     * @param array $messageData Notification data
     * @return bool
     */
    protected function validateMessageData(array $messageData): bool
    {
        return isset($messageData['title']) && isset($messageData['body']);
    }

    /**
     * Get localized text from multilingual array or string
     *
     * @param mixed $text Multilingual array or string
     * @param string $locale Preferred locale (default: app locale)
     * @return string Localized text
     */
    protected function getLocalizedText($text, string $locale = null): string
    {
        if (is_string($text)) {
            return $text;
        }

        if (is_array($text)) {
            $locale = $locale ?? app()->getLocale();

            // Try to get text for the current locale
            if (isset($text[$locale])) {
                return (string) $text[$locale];
            }

            // Fallback to English if available
            if (isset($text['en'])) {
                return (string) $text['en'];
            }

            // Fallback to Arabic if available
            if (isset($text['ar'])) {
                return (string) $text['ar'];
            }

            // Return first available text (ensure it's a string)
            $firstValue = reset($text);
            return is_string($firstValue) ? $firstValue : (string) $firstValue;
        }

        return '';
    }

    /**
     * Log FCM notification activity
     *
     * @param string $level Log level (info, warning, error, debug)
     * @param string $message Log message
     * @param array $context Additional context data
     */
    protected function logFCM(string $level, string $message, array $context = []): void
    {
        $loggingConfig = config('services.firebase.logging', []);

        // Check if logging is enabled
        if (!($loggingConfig['enabled'] ?? true)) {
            return;
        }

        // Check log level
        $allowedLevels = ['debug', 'info', 'warning', 'error'];
        $configLevel = $loggingConfig['log_level'] ?? 'info';
        $configLevelIndex = array_search($configLevel, $allowedLevels);
        $currentLevelIndex = array_search($level, $allowedLevels);

        if ($currentLevelIndex === false || $currentLevelIndex < $configLevelIndex) {
            return;
        }

        // Add FCM prefix to message
        $logMessage = "[FCM] {$message}";

        // Add timestamp and context
        $logContext = array_merge([
            'timestamp' => now()->toISOString(),
            'service' => 'FCM',
        ], $context);

        // Log based on level
        switch ($level) {
            case 'debug':
                Log::debug($logMessage, $logContext);
                break;
            case 'info':
                Log::info($logMessage, $logContext);
                break;
            case 'warning':
                Log::warning($logMessage, $logContext);
                break;
            case 'error':
                Log::error($logMessage, $logContext);
                break;
        }
    }

    /**
     * Log successful FCM notification
     *
     * @param array $tokens FCM tokens that were sent successfully
     * @param mixed $title Notification title (string or multilingual array)
     * @param mixed $body Notification body (string or multilingual array)
     * @param array $additionalData Additional context
     */
    protected function logFCMSuccess(array $tokens, $title, $body, array $additionalData = []): void
    {
        $loggingConfig = config('services.firebase.logging', []);

        if (!($loggingConfig['log_success'] ?? true)) {
            return;
        }

        // Format multilingual data for logging
        $titleLog = is_array($title) ? json_encode($title) : $title;
        $bodyLog = is_array($body) ? json_encode($body) : $body;

        $context = [
            'tokens_count' => count($tokens),
            'title' => $titleLog,
            'body' => $bodyLog,
            'status' => 'success',
        ];

        // Optionally log tokens (be careful with privacy)
        if ($loggingConfig['log_tokens'] ?? false) {
            $context['tokens'] = $tokens;
        } else {
            $context['token_preview'] = array_map(function($token) {
                return substr($token, 0, 20) . '...';
            }, $tokens);
        }

        $context = array_merge($context, $additionalData);

        $this->logFCM('info', 'Notification sent successfully', $context);
    }

    /**
     * Log failed FCM notification
     *
     * @param array $tokens FCM tokens that failed
     * @param string $error Error message
     * @param mixed $title Notification title (string or multilingual array)
     * @param mixed $body Notification body (string or multilingual array)
     * @param array $additionalData Additional context
     */
    protected function logFCMFailure(array $tokens, string $error, $title, $body, array $additionalData = []): void
    {
        $loggingConfig = config('services.firebase.logging', []);

        if (!($loggingConfig['log_failures'] ?? true)) {
            return;
        }

        // Format multilingual data for logging
        $titleLog = is_array($title) ? json_encode($title) : $title;
        $bodyLog = is_array($body) ? json_encode($body) : $body;

        $context = [
            'tokens_count' => count($tokens),
            'title' => $titleLog,
            'body' => $bodyLog,
            'error' => $error,
            'status' => 'failed',
        ];

        // Optionally log tokens (be careful with privacy)
        if ($loggingConfig['log_tokens'] ?? false) {
            $context['tokens'] = $tokens;
        } else {
            $context['token_preview'] = array_map(function($token) {
                return substr($token, 0, 20) . '...';
            }, $tokens);
        }

        $context = array_merge($context, $additionalData);

        $this->logFCM('error', 'Notification failed to send', $context);
    }

    /**
     * Log FCM token operations
     *
     * @param string $operation Operation type (stored, updated, removed, invalid)
     * @param string $token FCM token
     * @param array $additionalData Additional context
     */
    protected function logFCMToken(string $operation, string $token, array $additionalData = []): void
    {
        $loggingConfig = config('services.firebase.logging', []);

        if (!($loggingConfig['log_tokens'] ?? false)) {
            return;
        }

        $context = [
            'operation' => $operation,
            'token' => $token,
            'token_preview' => substr($token, 0, 20) . '...',
        ];

        $context = array_merge($context, $additionalData);

        $this->logFCM('debug', "FCM token {$operation}", $context);
    }

    /**
     * Send FCM notification to guest devices (non-authenticated users)
     *
     * @param array $fcmTokens Array of FCM tokens
     * @param array $messageData Notification data
     * @param string|null $modelType Model type for FCM data
     * @param int|null $modelId Model ID for FCM data
     * @return array Results of the notification sending
     */
    public function sendFCMToGuests(array $fcmTokens, array $messageData, ?string $modelType = null, ?int $modelId = null): array
    {
        $results = [];

        try {
            $messaging = $this->getFirebaseMessaging();

            // Get devices by FCM tokens
            $devices = \App\Models\Device::whereIn('fcm_token', $fcmTokens)
                ->whereNull('deviceable_type') // Guest devices
                ->ofActive()
                ->get();

            if ($devices->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No active guest devices found'
                ];
            }

            $tokens = $devices->pluck('fcm_token')->toArray();
            $deviceTypes = $devices->pluck('device_type')->toArray();

            $result = $this->sendToDevices($messaging, $tokens, $deviceTypes, $messageData, 'multiple', $modelType, $modelId);

            $results[] = [
                'success' => $result['success'],
                'message' => $result['message'],
                'tokens_sent' => count($tokens),
                'device_count' => $devices->count()
            ];

        } catch (Exception $e) {
            Log::error('FCM Guest Notification Error: ' . $e->getMessage());
            $results[] = [
                'success' => false,
                'message' => 'FCM guest notification error: ' . $e->getMessage()
            ];
        }

        return $results;
    }

    /**
     * Send FCM notification to all devices (authenticated + guest)
     *
     * @param array $messageData Notification data
     * @param string|null $modelType Model type for FCM data
     * @param int|null $modelId Model ID for FCM data
     * @return array Results of the notification sending
     */
    public function sendFCMToAllDevices(array $messageData, ?string $modelType = null, ?int $modelId = null): array
    {
        $results = [];

        try {
            $messaging = $this->getFirebaseMessaging();

            // Get all active devices with FCM tokens
            $devices = \App\Models\Device::ofActive()->withFcmToken()->get();

            if ($devices->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No active devices found'
                ];
            }

            // Group devices by type (authenticated vs guest)
            $authenticatedDevices = $devices->whereNotNull('deviceable_type');
            $guestDevices = $devices->whereNull('deviceable_type');

            $allTokens = $devices->pluck('fcm_token')->toArray();
            $allDeviceTypes = $devices->pluck('device_type')->toArray();

            $result = $this->sendToDevices($messaging, $allTokens, $allDeviceTypes, $messageData, 'multiple', $modelType, $modelId);

            $results[] = [
                'success' => $result['success'],
                'message' => $result['message'],
                'total_devices' => $devices->count(),
                'authenticated_devices' => $authenticatedDevices->count(),
                'guest_devices' => $guestDevices->count(),
                'tokens_sent' => count($allTokens)
            ];

        } catch (Exception $e) {
            Log::error('FCM All Devices Notification Error: ' . $e->getMessage());
            $results[] = [
                'success' => false,
                'message' => 'FCM all devices notification error: ' . $e->getMessage()
            ];
        }

        return $results;
    }
}
