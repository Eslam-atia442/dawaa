<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Illuminate\Http\ResponseTrait;
use Illuminate\Notifications\Notification;


class NotificationController extends BaseApiController
{
    public function __construct(NotificationService $service)
    {
        $this->service = $service;
        parent::__construct($service, NotificationResource::class);
    }

    public function getNotifications()
    {
        $response = $this->service->getNotifications('admin', 10);

        return $this->respondWithSuccess(__('Success'),
            [ 'notifications' => NotificationResource::collection($response['notifications']), 'unReaded' => $response['unReaded'] ]
        );

    }

    public function markAsRead($id)
    {
        auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
        return $this->respondWithSuccess(__('Success'));
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return $this->respondWithSuccess(__('Success'));
    }
}
