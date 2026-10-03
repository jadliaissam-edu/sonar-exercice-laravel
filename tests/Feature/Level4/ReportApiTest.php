<?php

namespace Tests\Feature\Level4;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_open_tickets_export_returns_csv(): void
    {
        $this->makeTicket(['title' => 'Broken keyboard']);

        $response = $this->get('/api/reports/open.csv');

        $response->assertOk();
        $this->assertStringContainsString('Broken keyboard', $response->getContent());
    }
}
