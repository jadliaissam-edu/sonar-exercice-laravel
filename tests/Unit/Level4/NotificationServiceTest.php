<?php

namespace Tests\Unit\Level4;

use App\Level4\Services\NotificationService;
use PHPUnit\Framework\TestCase;

class NotificationServiceTest extends TestCase
{
    public function test_closed_events_use_chat(): void
    {
        $service = new NotificationService();

        sleep(1);

        $this->assertEquals($service->channelFor('closed'), 'chat:Ticket resolved:1');
    }
}
