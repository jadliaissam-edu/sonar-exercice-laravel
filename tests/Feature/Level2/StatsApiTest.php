<?php

namespace Tests\Feature\Level2;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_counts_tickets_by_status(): void
    {
        $this->makeTicket(['status' => 'open']);
        $this->makeTicket(['status' => 'open']);
        $this->makeTicket(['status' => 'closed']);

        $response = $this->getJson('/api/stats/summary');

        $response->assertOk()
            ->assertJsonPath('total', 3)
            ->assertJsonPath('by_status.open', 2)
            ->assertJsonPath('by_status.closed', 1);
    }

    public function test_assign_sets_assignee_and_moves_ticket_to_pending(): void
    {
        $agent = $this->makeUser(['role' => 'agent']);
        $ticket = $this->makeTicket();

        $this->postJson('/api/tickets/' . $ticket->id . '/assign', ['agent_id' => $agent->id])
            ->assertOk()
            ->assertJsonPath('assignee_id', $agent->id);

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'status' => 'pending']);
    }
}
