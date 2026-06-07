<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        UserProfile::factory()->create(['user_id' => $user->id]);

        Company::factory(2)
            ->has(Project::factory(2)
                ->has(Ticket::factory(2)->state(['user_id' => $user->id]))
            )->create();

        Ticket::all()->each(function ($ticket) {
            TicketDetail::factory()->create(['ticket_id' => $ticket->id]);
        });
    }
}
