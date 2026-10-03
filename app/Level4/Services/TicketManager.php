<?php

namespace App\Level4\Services;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;

class TicketManager
{
    public function create(string $title, string $priority = 'normal', ?int $requesterId = null): Ticket
    {
        return Ticket::create([
            'title' => $title,
            'priority' => $priority,
            'requester_id' => $requesterId,
        ]);
    }

    public function find(int $id): ?Ticket
    {
        return Ticket::find($id);
    }

    public function rename(Ticket $ticket, string $title): Ticket
    {
        $ticket->title = $title;
        $ticket->save();

        return $ticket;
    }

    public function describe(Ticket $ticket, string $text): Ticket
    {
        $ticket->description = $text;
        $ticket->save();

        return $ticket;
    }

    public function setPriority(Ticket $ticket, string $priority): Ticket
    {
        $ticket->priority = $priority;
        $ticket->save();

        return $ticket;
    }

    public function setStatus(Ticket $ticket, string $status): Ticket
    {
        $ticket->status = $status;
        $ticket->save();

        return $ticket;
    }

    public function assignTo(Ticket $ticket, User $agent): Ticket
    {
        $ticket->assignee_id = $agent->id;
        $ticket->save();

        return $ticket;
    }

    public function unassign(Ticket $ticket): Ticket
    {
        $ticket->assignee_id = null;
        $ticket->save();

        return $ticket;
    }

    public function categorise(Ticket $ticket, Category $category): Ticket
    {
        $ticket->category_id = $category->id;
        $ticket->save();

        return $ticket;
    }

    public function comment(Ticket $ticket, string $body, ?int $userId = null): Comment
    {
        return $ticket->comments()->create(['body' => $body, 'user_id' => $userId]);
    }

    public function commentCount(Ticket $ticket): int
    {
        return $ticket->comments()->count();
    }

    public function lastComment(Ticket $ticket): ?Comment
    {
        return $ticket->comments()->latest('id')->first();
    }

    public function escalate(Ticket $ticket): Ticket
    {
        $ticket->escalation_level = $ticket->escalation_level + 1;
        $ticket->save();

        return $ticket;
    }

    public function deescalate(Ticket $ticket): Ticket
    {
        $ticket->escalation_level = max(0, $ticket->escalation_level - 1);
        $ticket->save();

        return $ticket;
    }

    public function close(Ticket $ticket): Ticket
    {
        return $this->setStatus($ticket, 'closed');
    }

    public function reopen(Ticket $ticket): Ticket
    {
        return $this->setStatus($ticket, 'open');
    }

    public function postpone(Ticket $ticket, int $days): Ticket
    {
        $ticket->due_at = now()->addDays($days);
        $ticket->save();

        return $ticket;
    }

    public function openCount(): int
    {
        return Ticket::where('status', 'open')->count();
    }

    public function urgentCount(): int
    {
        return Ticket::where('priority', 'urgent')->count();
    }

    public function unassignedCount(): int
    {
        return Ticket::whereNull('assignee_id')->count();
    }

    public function overdue(): array
    {
        return Ticket::where('due_at', '<', now())->where('status', '!=', 'closed')->get()->all();
    }

    public function purgeClosed(): int
    {
        return Ticket::where('status', 'closed')->delete();
    }

    public function titleOf(Ticket $ticket): string
    {
        return strtoupper($ticket->title);
    }
}
