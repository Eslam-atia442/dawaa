<?php

namespace App\Services\Notification;

use App\Enums\MailDriverEnum;
use App\Mail\general\GeneralMail;
use App\Services\AdminService;
use App\Services\BaseService;
use App\Services\UserService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class EmailService
{
    public AdminService $adminService;
    public UserService $userService;

    public function __construct(AdminService $adminService, UserService $userService)
    {
        $this->adminService = $adminService;
        $this->userService = $userService;
    }

    public function notify($request, $driver = MailDriverEnum::admin->value): void
    {
        $service = $driver == MailDriverEnum::admin->value ? $this->adminService : $this->userService;


        if ($request->notify == 'notifications') {
            $client = ('all' == $request->id) ? User::latest()->get() : User::findOrFail($request->id);
            Notification::send($client, new NotifyUser($request->all()));
        } else {
            $mails = [];
             if ($request->id)
                $mails[] = $service->find($request->id)->email;
            else
                $mails =  $service->search()->pluck('email')->toArray();


            foreach ($mails as $mail) {
                Mail::to($mail)->send(new GeneralMail(['title' => 'اشعار اداري', 'message' => $request->message]));

            }

        }

    }
}
