<?php

namespace Tests\Feature\Level5;

use App\Level5\Support\Metrics;
use App\Level5\Support\TicketRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        (new Metrics())->reset();
        TicketRegistry::getInstance()->forget();
    }

    public function test_open_ticket_can_move_to_pending(): void
    {
        $ticket = $this->makeTicket();

        $this->postJson('/api/workflow/' . $ticket->id . '/transition', ['to' => 'pending'])
            ->assertOk()
            ->assertJsonPath('status', 'pending');

        $this->assertSame(1, TicketRegistry::getInstance()->count());
        $this->assertSame(1, (new Metrics())->total());
    }

    public function test_closing_clears_due_date(): void
    {
        $ticket = $this->makeTicket(['due_at' => now()->addDay()]);

        $this->postJson('/api/workflow/' . $ticket->id . '/transition', ['to' => 'closed'])->assertOk();

        $this->assertNull($ticket->fresh()->due_at);
    }

    public function test_invalid_transition_is_rejected(): void
    {
        $ticket = $this->makeTicket(['status' => 'closed']);

        $this->postJson('/api/workflow/' . $ticket->id . '/transition', ['to' => 'pending'])
            ->assertStatus(422);
    }

    public function test_unknown_status_is_rejected(): void
    {
        $ticket = $this->makeTicket(['status' => 'archived']);

        $this->postJson('/api/workflow/' . $ticket->id . '/transition', ['to' => 'open'])
            ->assertStatus(422)
            ->assertJsonPath('error', 'Unknown status archived');
    }

    public function test_escalation_increments_level(): void
    {
        $ticket = $this->makeTicket();

        $this->postJson('/api/workflow/' . $ticket->id . '/escalate')
            ->assertOk()
            ->assertJsonPath('level', 1);
    }

    public function test_closed_ticket_cannot_be_escalated(): void
    {
        $ticket = $this->makeTicket(['status' => 'closed']);

        $this->postJson('/api/workflow/' . $ticket->id . '/escalate')->assertStatus(422);
    }

    public function test_priority_weight_is_returned(): void
    {
        $this->getJson('/api/workflow/priority?value=URGENT')
            ->assertOk()
            ->assertJsonPath('weight', 4);
    }
}
