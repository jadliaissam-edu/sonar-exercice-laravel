<?php

namespace App\Level5\Support;

class NullNotifier implements Notifier
{
    public function send(string $to, string $message): void
    {
    }
}
