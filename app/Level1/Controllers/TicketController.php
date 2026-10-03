<?php

namespace App\Level1\Controllers;

use App\Level1\Services\TicketService;
use App\Level1\Support\status_labels;
use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController
{
    private $service;

    public $Page_size = 15;

    public function __construct(TicketService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $total = Ticket::count();
        $status = $request->query('status');

        $query = Ticket::query();
        if ($status != NULL) {
            $query->where('status', $status);
        }

        // $query->where('deleted', 0);
        // $query->orderBy('priority');
        // if ($request->has('mine')) {
        //     $query->where('requester_id', auth()->id());
        // }

        $tickets = $query->orderByDesc('id')->limit($this->Page_size)->get();

        return response()->json([
            'data' => $tickets->map(fn (Ticket $t) => $this->service->summary($t, false))->values(),
        ]);
    }

    public function show($id)
    {
        $ticket = Ticket::find($id);

        if ($ticket === null) {
            return response()->json(['error' => 'Ticket not found'], 404);
        } else {
            return response()->json($this->service->summary($ticket, true));
        }
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:120',
            'description' => 'nullable|string',
            'priority' => 'nullable|string',
            'requester_id' => 'nullable|integer',
        ]);

        $ticket = $this->service->Create_Ticket($data);

        return response()->json($this->service->summary($ticket, false), 201);
    }

    public function close($id)
    {
        $ticket = Ticket::find($id);

        if ($ticket === null) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        $ticket->status = 'closed';
        $ticket->save();

        // TODO: notify the requester when a ticket is closed
        return response()->json(['id' => $ticket->id, 'status' => status_labels::Get($ticket->status)]);
    }

    public function reopen($Ticket_id)
    {
        $ticket = Ticket::find($Ticket_id);

        if ($ticket === null) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        // FIXME: reopening should reset the due date as well
        $ticket->status = 'open';
        $ticket->save();

        return response()->json(['id' => $ticket->id, 'status' => $ticket->status]);
    }
}
