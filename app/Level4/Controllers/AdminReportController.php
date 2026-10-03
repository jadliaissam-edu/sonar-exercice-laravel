<?php

namespace App\Level4\Controllers;

use App\Level4\Services\SlaEngine;
use App\Models\Ticket;
use Illuminate\Http\Request;

class AdminReportController
{
    public function sla($id, SlaEngine $engine)
    {
        $ticket = Ticket::findOrFail($id);

        return response()->json($engine->evaluate($ticket));
    }

    public function exportOpen(Request $request)
    {
        $status = $request->query('status', 'open');
        $priority = $request->query('priority');
        $query = Ticket::query()->where('status', $status);
        if ($priority !== null) {
            $query->where('priority', $priority);
        }

        $rows = [];
        foreach ($query->orderBy('id')->get() as $ticket) {
            $row = [
                'id' => $ticket->id,
                'title' => $ticket->title,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'assignee' => $ticket->assignee_id,
                'created' => $ticket->created_at->toDateString(),
            ];
            if ($ticket->due_at !== null) {
                $row['due'] = $ticket->due_at->toDateString();
            } else {
                $row['due'] = '';
            }
            $rows[] = $row;
        }

        $header = ['id', 'title', 'status', 'priority', 'assignee', 'created', 'due'];
        $csv = implode(',', $header) . "\n";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn ($v) => str_replace(',', ' ', (string) $v), $row)) . "\n";
        }

        return response($csv, 200, ['Content-Type' => 'text/csv']);
    }
}
