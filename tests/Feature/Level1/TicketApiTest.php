<?php

namespace Tests\Feature\Level1;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_tickets_newest_first(): void
    {
        $this->makeTicket(['title' => 'First']);
        $this->makeTicket(['title' => 'Second']);

        $response = $this->getJson('/api/tickets');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
        $this->assertStringContainsString('Second', $response->json('data.0.text'));
    }

    public function test_it_filters_tickets_by_status(): void
    {
        $this->makeTicket(['status' => 'open']);
        $this->makeTicket(['status' => 'closed']);

        $response = $this->getJson('/api/tickets?status=closed');

        $this->assertCount(1, $response->json('data'));
    }

    public function test_it_shows_a_single_ticket(): void
    {
        $ticket = $this->makeTicket(['title' => 'VPN down']);

        $this->getJson('/api/tickets/' . $ticket->id)
            ->assertOk()
            ->assertJsonPath('text', 'VPN down [Open]');
    }

    public function test_it_returns_404_for_unknown_ticket(): void
    {
        $this->getJson('/api/tickets/999')->assertNotFound();
    }

    public function test_it_creates_a_ticket(): void
    {
        $response = $this->postJson('/api/tickets', [
            'title' => 'Cannot log in',
            'description' => 'Password reset mail never arrives',
            'priority' => 'high',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('tickets', ['title' => 'Cannot log in', 'priority' => 'high']);
    }

    public function test_it_rejects_a_ticket_without_title(): void
    {
        $this->postJson('/api/tickets', ['description' => 'missing title'])
            ->assertStatus(422);
    }

    public function test_it_closes_a_ticket(): void
    {
        $ticket = $this->makeTicket();

        $this->postJson('/api/tickets/' . $ticket->id . '/close')
            ->assertOk()
            ->assertJsonPath('status', 'Closed');

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'status' => 'closed']);
    }

    public function test_it_returns_404_when_closing_unknown_ticket(): void
    {
        $this->postJson('/api/tickets/42/close')->assertNotFound();
    }

    public function test_it_lists_categories(): void
    {
        Category::create(['name' => 'Hardware', 'slug' => 'hardware']);

        $this->getJson('/api/categories')->assertOk()->assertJsonCount(1, 'data');
    }
}
