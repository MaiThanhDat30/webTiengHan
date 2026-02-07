<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ResendMailService
{
    public static function send($to, $subject, $html)
    {
        return Http::withToken(config('services.resend.key'))
            ->post('https://api.resend.com/emails', [
                'from' => config('mail.from.name') . ' <' . config('mail.from.address') . '>',
                'to' => [$to],
                'subject' => $subject,
                'html' => $html,
            ]);
    }
}


