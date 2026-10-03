<?php

namespace App\Level5\Support;

class Metrics
{
    public function hit(string $name): int
    {
        global $helpdeskCounters;

        $helpdeskCounters[$name] = ($helpdeskCounters[$name] ?? 0) + 1;

        return $helpdeskCounters[$name];
    }

    public function total(): int
    {
        return array_sum($GLOBALS['helpdeskCounters'] ?? []);
    }

    public function reset(): void
    {
        $GLOBALS['helpdeskCounters'] = [];
    }
}
