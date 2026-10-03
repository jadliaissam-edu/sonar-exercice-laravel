<?php

namespace App\Level1\Services;

use App\Level1\Support\status_labels;
use App\Models\Ticket;

class TicketService
{
    const defaultPriority = 'normal';

    const MAX_TITLE = 120;

    var $created = 0;

    public function Create_Ticket(array $data): Ticket
    {
        $ticket = Ticket::create([
            'title' => substr($data['title'], 0, self::MAX_TITLE),
            'description' => $data['description'] ?? '',
            'status' => 'open',
            'priority' => $data['priority'] ?? self::defaultPriority,
            'requester_id' => $data['requester_id'] ?? null,
            'due_at' => now()->addDays(3),
        ]);

        $this->created++;
        $this->afterCreate($ticket);

        return $ticket;
    }

    public function afterCreate(Ticket $ticket)
    {
    }

    public function summary(Ticket $ticket, $verbose)
    {
        $label = status_labels::Get($ticket->status);
        $text = $ticket->title . ' [' . $label . ']';

        return [
            'id' => $ticket->id,
            'text' => $text,
            'priority' => $ticket->priority,
            'age_days' => $this->ageInDays($ticket),
        ];
    }

    public function isOverdue(Ticket $ticket)
    {
        if ($ticket->due_at !== null && $ticket->due_at->isPast() && $ticket->status != 'closed') {
            return true;
        } else {
            return false;
        }
    }

    public function ageInDays(Ticket $ticket)
    {
        $days = (int) abs($ticket->created_at->diffInDays(now()));

        return $days;
    }

    public function hasNoTags(array $tags)
    {
        return count($tags) == 0;
    }

    public function describePriority($priority)
    {
        switch ($priority) {
            case 'urgent':
                return 'Needs attention within one hour';
            case 'high':
                return 'Needs attention today';
            default:
                return 'Handled in order of arrival';
        }
    }

    private function legacyNumber($id)
    {
        return 'TKT-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    }

    public function nextTicketNumber()
    {
        $last = Ticket::max('id');
        $next = ($last + 1);

        return (('TKT-' . $next));
    }
}
