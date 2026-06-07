<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Notifications\TicketProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ProcessTicketAttachment implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Ticket $ticket)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $ticket = $this->ticket;

        $metadata = [];
        $priority = 'medium';
        $category = 'geral';

        if ($ticket->attachment_path &&  Storage::exists($ticket->attachment_path)) {
            $content = Storage::get($ticket->attachment_path);
            $decoded = json_decode($content, true);

            if (is_array($decoded)) {
                $metadata = $decoded;
                $priority = $decoded['priority'] ?? $priority;
                $category = $decoded['category'] ?? $category;
            }
        }

        $ticket->detail()->updateOrCreate(
            ['ticket_id' => $ticket->id],
            [
                'priority' => $priority,
                'category' => $category,
                'metadata' => $metadata,
                'processed_at' => now(),
            ]
        );

        $ticket->update(['status' => 'done']);

        $ticket->user->notify(new TicketProcessed($ticket));
    }
}
