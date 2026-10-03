<?php

namespace App\Level5\Support;

interface Notifier
{
    public function send(string $to, string $message): void;
}
