<?php

namespace Tests\Feature\Level2;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_adds_a_comment_to_a_ticket(): void
    {
        $ticket = $this->makeTicket();

        $response = $this->postJson('/api/tickets/' . $ticket->id . '/comments', [
            'body' => '  Please restart the router  ',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('comments', ['ticket_id' => $ticket->id, 'body' => 'Please restart the router']);
    }

    public function test_it_lists_comments_in_order(): void
    {
        $ticket = $this->makeTicket();
        $ticket->comments()->create(['body' => 'one']);
        $ticket->comments()->create(['body' => 'two']);

        $response = $this->getJson('/api/tickets/' . $ticket->id . '/comments');

        $this->assertSame('one', $response->json('data.0.body'));
    }

    public function test_it_requires_a_body(): void
    {
        $ticket = $this->makeTicket();

        $this->postJson('/api/tickets/' . $ticket->id . '/comments', [])->assertStatus(422);
    }

    public function test_it_deletes_a_comment(): void
    {
        $ticket = $this->makeTicket();
        $comment = $ticket->comments()->create(['body' => 'obsolete']);

        $this->deleteJson('/api/tickets/' . $ticket->id . '/comments/' . $comment->id)->assertNoContent();
    }
}
