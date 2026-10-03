<?php

namespace Tests\Unit\Level1;

use App\Level1\Services\TicketService;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_overdue_when_due_date_has_passed(): void
    {
        $ticket = $this->makeTicket(['due_at' => now()->subDay()]);

        $this->assertTrue((new TicketService())->isOverdue($ticket));
    }

    public function test_not_overdue_when_closed(): void
    {
        $ticket = $this->makeTicket(['due_at' => now()->subDay(), 'status' => 'closed']);

        $this->assertFalse((new TicketService())->isOverdue($ticket));
    }

    public function test_age_in_days_is_zero_for_new_ticket(): void
    {
        $ticket = $this->makeTicket();

        $this->assertSame(0, (new TicketService())->ageInDays($ticket));
    }

    public function test_describe_priority_for_urgent(): void
    {
        $this->assertSame('Needs attention within one hour', (new TicketService())->describePriority('urgent'));
    }

    public function test_has_no_tags(): void
    {
        $service = new TicketService();

        $this->assertTrue($service->hasNoTags([]));
        $this->assertFalse($service->hasNoTags(['printer']));
    }

    public function test_create_ticket_truncates_long_titles(): void
    {
        $ticket = (new TicketService())->Create_Ticket(['title' => str_repeat('a', 200)]);

        $this->assertInstanceOf(Ticket::class, $ticket);
        $this->assertSame(TicketService::MAX_TITLE, strlen($ticket->title));
    }
}
