<?php

namespace Tests\Feature\Level3;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_finds_tickets_by_title(): void
    {
        $this->makeTicket(['title' => 'Projector bulb is dead']);
        $this->makeTicket(['title' => 'Wi-Fi drops every hour']);

        $response = $this->getJson('/api/search?q=Projector');

        $response->assertOk()->assertJsonCount(1, 'data');
    }
}
