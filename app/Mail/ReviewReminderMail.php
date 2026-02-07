<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReviewReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public int $count;
    public $vocabs;

    public function __construct(User $user, int $count, $vocabs)
    {
        $this->user = $user;
        $this->count = $count;
        $this->vocabs = $vocabs;
    }

    public function build()
    {
        return $this
            ->subject("📚 {$this->user->name}, bạn có từ cần ôn hôm nay")
            ->view('emails.review-reminder');
    }
}
