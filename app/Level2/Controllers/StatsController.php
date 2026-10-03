<?php

namespace App\Level2\Controllers;

use App\Level2\Services\TicketAssignmentService;
use App\Level2\Services\TicketStatsService;
use App\Models\Ticket;
use Illuminate\Http\Request;

class StatsController
{
    public function summary(TicketStatsService $stats)
    {
        return response()->json($stats->summary());
    }

    public function assign(Request $request, $ticketId, TicketAssignmentService $assignments)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $agent = $assignments->findAgentFor($request->input('agent_id'));

        $assignments->assign($ticket, $agent);

        return response()->json(['id' => $ticket->id, 'assignee_id' => $ticket->assignee_id]);
    }
}
