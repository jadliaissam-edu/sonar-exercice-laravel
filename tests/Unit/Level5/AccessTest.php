<?php

namespace Tests\Unit\Level5;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AccessTest extends TestCase
{
    public function test_agents_can_manage_tickets(): void
    {
        $agent = new User(['role' => 'agent']);

        $this->assertTrue(Gate::forUser($agent)->allows('manage-tickets'));
    }

    public function test_customers_cannot_manage_tickets(): void
    {
        $customer = new User(['role' => 'customer']);

        $this->assertFalse(Gate::forUser($customer)->allows('manage-tickets'));
    }
}
