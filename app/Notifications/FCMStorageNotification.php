<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Store FCM notification in database for User to retrieve via API.
 * Created when FCM is successfully sent to a user (deviceable_type=User).
 */
class FCMStorageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public array  $messageData,
        public ?string $modelType = null,
        public ?int    $modelId = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $title = $this->messageData['title'] ?? '';
        $body = $this->messageData['body'] ?? '';

        $data = [
            'title' => is_array($title) ? ($title['en'] ?? $title['ar'] ?? '') : $title,
            'body' => is_array($body) ? ($body['en'] ?? $body['ar'] ?? '') : $body,
            'title_ar' => is_array($title) ? ($title['ar'] ?? '') : $title,
            'title_en' => is_array($title) ? ($title['en'] ?? '') : $title,
            'body_ar' => is_array($body) ? ($body['ar'] ?? '') : $body,
            'body_en' => is_array($body) ? ($body['en'] ?? '') : $body,
            'model_type' => $this->modelType,
            'model_id' => $this->modelId,
        ];

        if (isset($this->messageData['data']) && is_array($this->messageData['data'])) {
            $data['extra'] = $this->messageData['data'];
        }

        return $data;
    }
}
