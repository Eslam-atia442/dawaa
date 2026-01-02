<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class NotificationResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request) : array
    {
        $this->micro = [
            'id' => $this->id,
        ];

        $this->full = [
            'message' => $this->data['message_'.app()->getLocale()] ?? '',
            'type' => $this->data['type'] ?? '',
            'model' => $this->data['model'] ?? '',
            'model_id' => $this->data['model_id'] ?? '',
            'is_read' => (bool)$this->read_at,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at->diffForHumans(),
            'url' => adminNotificationLink($this->data),
        ];

        //$this->relationLoaded()
        $this->relations = [
        ];
        return $this->getResource();
    }
}
