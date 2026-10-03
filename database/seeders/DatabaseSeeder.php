<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $agent = User::factory()->create([
            'name' => 'Ada Agent',
            'email' => 'agent@example.com',
            'role' => 'agent',
        ]);

        $customer = User::factory()->create([
            'name' => 'Carl Customer',
            'email' => 'customer@example.com',
        ]);

        $hardware = Category::create(['name' => 'Hardware', 'slug' => 'hardware']);
        Category::create(['name' => 'Accounts', 'slug' => 'accounts']);

        $samples = [
            ['Laptop does not boot', 'open', 'urgent'],
            ['Cannot reset my password', 'pending', 'high'],
            ['Request for second monitor', 'open', 'low'],
            ['Printer offline in room 12', 'closed', 'normal'],
        ];

        foreach ($samples as [$title, $status, $priority]) {
            Ticket::create([
                'title' => $title,
                'description' => 'Reported through the helpdesk form.',
                'status' => $status,
                'priority' => $priority,
                'category_id' => $hardware->id,
                'requester_id' => $customer->id,
                'assignee_id' => $status === 'open' ? null : $agent->id,
                'due_at' => now()->addDays(2),
            ]);
        }
    }
}
