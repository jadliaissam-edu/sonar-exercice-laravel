<?php

namespace App\Level2\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\Request;

class CommentController
{
    public function index($ticketId)
    {
        $comments = Comment::where('ticket_id', $ticketId)->orderBy('id')->get();

        return response()->json(['data' => $comments]);
    }

    public function store(Request $request, $ticketId)
    {
        $data = $request->validate([
            'body' => 'required|string',
            'user_id' => 'nullable|integer',
            'internal' => 'nullable|boolean',
        ]);

        $ticket = Ticket::find($ticketId);

        $comment = $ticket->comments()->create([
            'user_id' => $data['user_id'] ?? null,
            'body' => trim($data['body']),
            'internal' => $data['internal'] ?? false,
        ]);

        return response()->json($comment, 201);
    }

    public function destroy($ticketId, $commentId)
    {
        $comment = Comment::find($commentId);

        if ($comment->ticket_id == $ticketId) {
            $comment->delete();

            return response()->json(null, 204);
        }

        return response()->json(['error' => 'Comment does not belong to this ticket'], 422);
    }
}
