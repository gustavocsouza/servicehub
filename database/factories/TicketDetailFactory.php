<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketDetail>
 */
class TicketDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'category' => fake()->word(),
            'metadata' => ['source' => 'factory'],
            'processed_at' => null,
        ];
    }
}
