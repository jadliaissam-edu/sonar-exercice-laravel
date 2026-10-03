<?php

namespace App\Level5\Support;

use App\Models\Ticket;

class SlaCalculator
{
    public function firstResponseHours(string $priority): int
    {
        $table = config('helpdesk.sla_hours');

        return $table[$priority] ?? 24;
    }

    public function resolutionHours(string $priority): int
    {
        $table = config('helpdesk.sla_hours');

        return $table[$priority] ?? 24;
    }

    public function dueAt(Ticket $ticket)
    {
        return $ticket->created_at->copy()->addHours($this->resolutionHours($ticket->priority));
    }
}
