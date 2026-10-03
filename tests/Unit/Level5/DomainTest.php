<?php

namespace Tests\Unit\Level5;

use App\Level5\Domain\WorkflowTicket;
use App\Level5\Enums\Priority;
use Tests\TestCase;

class DomainTest extends TestCase
{
    public function test_workflow_ticket_exposes_loaded_attributes(): void
    {
        $record = new WorkflowTicket(['status' => 'pending', 'assignee_id' => 7]);

        $this->assertSame('pending', $record->status());
        $this->assertSame(7, $record->assigneeId());
        $this->assertSame(['loaded'], $record->trail());
    }

    public function test_record_can_be_locked_and_versioned(): void
    {
        $record = new WorkflowTicket();
        $record->lock();
        $record->bump();

        $this->assertTrue($record->isLocked());
        $this->assertSame(2, $record->version());
    }

    public function test_priority_weights(): void
    {
        $this->assertSame(1, Priority::Low->weight());
        $this->assertSame(3, Priority::High->weight());
        $this->assertTrue(Priority::Urgent->isEscalated());
        $this->assertFalse(Priority::Normal->isEscalated());
    }
}
