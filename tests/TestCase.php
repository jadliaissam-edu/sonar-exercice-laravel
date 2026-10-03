<?php

namespace Tests;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function makeUser(array $overrides = []): User
    {
        return User::factory()->create($overrides);
    }

    protected function makeTicket(array $overrides = []): Ticket
    {
        return Ticket::create(array_merge([
            'title' => 'Printer on floor 2 is jammed',
            'description' => 'Paper tray keeps getting stuck',
            'status' => 'open',
            'priority' => 'normal',
        ], $overrides));
    }
}
