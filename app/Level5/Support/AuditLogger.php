<?php

namespace App\Level5\Support;

use Illuminate\Support\Facades\Log;

class AuditLogger
{
    private $channel = 'audit';

    private $lastMessage;

    public function record(string $action, int $ticketId): void
    {
        Log::info('audit ' . $action . ' ticket ' . $ticketId);
    }

    public function tail(string $file): array
    {
        $lines = @file($file, FILE_IGNORE_NEW_LINES);

        return $lines === false ? [] : array_slice($lines, -10);
    }
}
