<?php

namespace App\Mail;

use Resend;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;

class ResendTransport extends AbstractTransport
{
    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        Resend::emails()->send([
            'from' => config('mail.from.address'),
            'to' => collect($email->getTo())->keys()->toArray(),
            'subject' => $email->getSubject(),
            'html' => $email->getHtmlBody(),
        ]);
    }

    /**
     * 🔧 BẮT BUỘC PHẢI CÓ
     */
    public function __toString(): string
    {
        return 'resend';
    }
}
