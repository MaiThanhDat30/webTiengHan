<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ResendMailService
{
    public static function send($to, $subject, $html)
    {
        $response = Http::withToken(config('services.resend.key'))
            ->post('https://api.resend.com/emails', [
                // EMAIL TEST CHÍNH THỨC CỦA RESEND
                'from' => 'onboarding@resend.dev',
                'to' => [$to],
                'subject' => $subject,
                'html' => $html,
            ]);

        // DEBUG nếu gửi fail
        if (! $response->successful()) {
            logger()->error('Resend error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }

        return $response;
    }
}
