<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserNotificationResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        $data = $this->data ?? [];
        $locale = app()->getLocale();

        $title = $data['title_' . $locale] ?? $data['title'] ?? '';
        $body = $data['body_' . $locale] ?? $data['body'] ?? '';

        $this->micro = [
            'id' => $this->id,
            'title' => $title,
        ];

        $this->full = [
            'id' => $this->id,
            'title' => $title,
            'body' => $body,
            'title_ar' => $data['title_ar'] ?? $data['title'] ?? '',
            'title_en' => $data['title_en'] ?? $data['title'] ?? '',
            'body_ar' => $data['body_ar'] ?? $data['body'] ?? '',
            'body_en' => $data['body_en'] ?? $data['body'] ?? '',
            'model_type' => $data['model_type'] ?? null,
            'model_id' => $data['model_id'] ?? null,
            'extra' => $data['extra'] ?? [],
            'is_read' => (bool) $this->read_at,
            'read_at' => $this->read_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];

        $this->relations = [];

        return $this->getResource();
    }
}
