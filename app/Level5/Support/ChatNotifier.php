<?php

namespace App\Level5\Support;

use Illuminate\Support\Facades\Log;

class ChatNotifier implements Notifier
{
    public function send(string $to, string $message): void
    {
        Log::info('chat to ' . $to . ': ' . $message);
    }
}
