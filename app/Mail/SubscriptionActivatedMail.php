<?php

namespace App\Mail;

use App\Models\SubscriptionPackage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $pkg;
    public $content;

    public function __construct($user, SubscriptionPackage $pkg, array $content)
    {
        $this->user = $user;
        $this->pkg = $pkg;
        $this->content = $content;
    }

    public function build()
    {
        return $this->subject('JodohMurni - Subscription Aktif & Ebook')
            ->view('emails.subscription-activated');
    }
}
