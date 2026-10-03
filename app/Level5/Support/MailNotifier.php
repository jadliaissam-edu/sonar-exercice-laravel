<?php

namespace App\Level5\Support;

use Illuminate\Support\Facades\Log;

class MailNotifier implements Notifier
{
    public function send(string $to, string $message): void
    {
        Log::info('mail to ' . $to . ': ' . $message);
    }
}
