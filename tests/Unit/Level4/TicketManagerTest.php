<?php

namespace Tests\Unit\Level4;

use App\Level4\Services\TicketManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_and_find(): void
    {
        $manager = new TicketManager();

        $ticket = $manager->create('Broken monitor', 'high');

        $this->assertSame('high', $manager->find($ticket->id)->priority);
    }

    public function test_close_sets_status(): void
    {
        $manager = new TicketManager();
        $ticket = $manager->create('Broken mouse');

        $this->assertSame('closed', $manager->close($ticket)->status);
    }

    public function test_open_count(): void
    {
        $manager = new TicketManager();
        $manager->create('One');
        $manager->create('Two');

        $this->assertSame(2, $manager->openCount());
    }
}
