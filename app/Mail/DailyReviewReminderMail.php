<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyReviewReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $items;

    public function __construct($user, $items)
    {
        $this->user  = $user;
        $this->items = $items;
    }

    public function build()
    {
        return $this->subject('📚 Đến giờ ôn từ vựng rồi!')
            ->view('emails.daily-review-reminder');
    }
}
