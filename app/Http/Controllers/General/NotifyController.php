<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Services\Notification\EmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotifyController extends Controller
{
    public EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function notify(Request $request, $driver = null): JsonResponse
    {
        $this->emailService->notify($request, $driver);
        return response()->json('done');
    }
}
