<?php

namespace App\Level5\Controllers;

use App\Level5\Enums\Priority;
use App\Level5\Services\WorkflowService;
use App\Models\Ticket;
use Illuminate\Http\Request;

class WorkflowController
{
    public function transition(Request $request, $id, WorkflowService $workflow)
    {
        $ticket = Ticket::findOrFail($id);

        try {
            $ticket = $workflow->transition($ticket, (string) $request->input('to'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['id' => $ticket->id, 'status' => $ticket->status]);
    }

    public function escalate($id, WorkflowService $workflow)
    {
        $ticket = Ticket::findOrFail($id);

        try {
            $workflow->escalate($ticket);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['id' => $ticket->id, 'level' => $ticket->escalation_level]);
    }

    public function priority(Request $request)
    {
        $priority = Priority::parse((string) $request->query('value', 'normal'));

        return response()->json(['priority' => $priority->value, 'weight' => $priority->weight()]);
    }
}
