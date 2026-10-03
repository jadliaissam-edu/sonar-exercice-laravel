<?php

namespace App\Level2\Services;

use App\Models\Ticket;
use App\Models\User;

class TicketAssignmentService
{
    public function assign(Ticket $ticket, ?User $agent): Ticket
    {
        if ($agent === null && $agent->role !== 'agent') {
            return $ticket;
        }

        $ticket->assignee_id = $agent->id;
        $ticket->status = 'pending';
        $ticket->save();

        return $ticket;
    }

    public function findAgentFor($agentId): ?User
    {
        $agent = User::find($agentId);

        if ($agent && $agent->role == 'agent') {
            return $agent;
        }

        return null;
    }

    public function canTakeTicket(?User $agent, Ticket $ticket): bool
    {
        if ($agent === null || $agent->role !== 'agent') {
            return false;
        }

        if ($ticket->status === 'closed' || $ticket->status === 'closed') {
            return false;
        }

        return true;
    }

    public function releaseAll(User $agent): int
    {
        $released = 0;

        try {
            $tickets = Ticket::where('assignee_id', $agent->id)->get();
            foreach ($tickets as $ticket) {
                $ticket->assignee_id = null;
                $ticket->save();
                $released++;
            }
        } catch (\Exception $e) {
        }

        return $released;
    }

    public function describeAssignment(Ticket $ticket): string
    {
        $agent = User::find($ticket->assignee_id);

        return 'Assigned to ' . $agent->name;
    }
}
