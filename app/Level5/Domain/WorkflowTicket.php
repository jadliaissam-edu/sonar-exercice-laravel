<?php

namespace App\Level5\Domain;

use App\Models\Ticket;

class WorkflowTicket extends AssignableRecord
{
    protected function boot(): void
    {
        parent::boot();
        $this->assigneeId = $this->get('assignee_id');
    }

    public static function fromModel(Ticket $ticket): self
    {
        return new self($ticket->only(['id', 'status', 'priority', 'assignee_id']));
    }

    public function status(): string
    {
        return (string) $this->get('status', 'open');
    }
}
