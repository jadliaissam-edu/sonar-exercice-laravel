<?php

namespace Tests\Unit\Level2;

use App\Level2\Services\TicketAssignmentService;
use App\Level2\Services\TicketStatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketStatsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_weight_by_priority(): void
    {
        $service = new TicketStatsService();

        $this->assertSame(10, $service->weight('urgent'));
        $this->assertSame(5, $service->weight('normal'));
        $this->assertSame(1, $service->weight('low'));
    }

    public function test_status_score(): void
    {
        $service = new TicketStatsService();

        $this->assertSame(2, $service->statusScore('pending'));
        $this->assertSame(3, $service->statusScore('closed'));
    }

    public function test_running_total_of_values(): void
    {
        $this->assertGreaterThan(0, (new TicketStatsService())->runningTotal([1, 2, 3]));
    }

    public function test_is_empty(): void
    {
        $service = new TicketStatsService();

        $this->assertTrue($service->isEmpty([]));
        $this->assertFalse($service->isEmpty([1]));
    }

    public function test_pagination_offset_for_first_page(): void
    {
        $this->assertSame(0, (new TicketStatsService())->paginate(1, 25)['offset']);
    }

    public function test_load_label(): void
    {
        $this->assertSame('busy', (new TicketStatsService())->loadLabel(20));
    }

    public function test_closed_count(): void
    {
        $this->makeTicket(['status' => 'closed']);

        $this->assertSame(1, (new TicketStatsService())->closedCount());
    }

    public function test_first_open_ticket(): void
    {
        $ticket = $this->makeTicket();

        $this->assertSame($ticket->id, (new TicketStatsService())->firstOpen()->id);
    }

    public function test_parse_due_date(): void
    {
        $this->assertNotNull((new TicketStatsService())->parseDueDate('2030-01-01'));
    }

    public function test_hot_tickets_are_urgent_ones(): void
    {
        $ticket = $this->makeTicket(['priority' => 'urgent']);

        $this->assertTrue((new TicketStatsService())->isHot($ticket));
    }

    public function test_agent_can_take_open_ticket(): void
    {
        $agent = $this->makeUser(['role' => 'agent']);
        $ticket = $this->makeTicket();

        $this->assertTrue((new TicketAssignmentService())->canTakeTicket($agent, $ticket));
    }

    public function test_customer_cannot_take_ticket(): void
    {
        $customer = $this->makeUser();
        $ticket = $this->makeTicket();

        $this->assertFalse((new TicketAssignmentService())->canTakeTicket($customer, $ticket));
    }

    public function test_release_all_unassigns_tickets(): void
    {
        $agent = $this->makeUser(['role' => 'agent']);
        $this->makeTicket(['assignee_id' => $agent->id]);
        $this->makeTicket(['assignee_id' => $agent->id]);

        $this->assertSame(2, (new TicketAssignmentService())->releaseAll($agent));
    }
}
