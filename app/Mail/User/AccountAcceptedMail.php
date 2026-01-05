<?php

namespace App\Mail\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountAcceptedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public string $password;
    public string $locale;

    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
        $this->locale = $user->locale ?? config('app.locale', 'ar');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('trans.account_accepted_email_subject', [], $this->locale),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user.account-accepted',
            with: [
                'user' => $this->user,
                'password' => $this->password,
                'locale' => $this->locale,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
