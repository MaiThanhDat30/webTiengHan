<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ResendMailService
{
    public static function send($to, $subject, $html)
    {
        return Http::withToken(config('services.resend.key'))
            ->post('https://api.resend.com/emails', [
                'from' => 'Vocab App <no-reply@your-domain.com>',
                'to' => [$to],
                'subject' => $subject,
                'html' => $html,
            ]);
    }
}
