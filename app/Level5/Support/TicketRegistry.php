<?php

namespace App\Level5\Support;

use App\Models\Ticket;

class TicketRegistry
{
    private static ?TicketRegistry $instance = null;

    private array $tickets = [];

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function remember(Ticket $ticket): void
    {
        $this->tickets[$ticket->id] = $ticket;
    }

    public function count(): int
    {
        return count($this->tickets);
    }

    public function forget(): void
    {
        $this->tickets = [];
    }
}
