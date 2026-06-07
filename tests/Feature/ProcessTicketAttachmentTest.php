<?php

use App\Jobs\ProcessTicketAttachment;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketProcessed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('processa o anexo, enriquece o detail e notifica o responsável', function () {
    Notification::fake();
    Storage::fake();

    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $path = 'attachments/teste.json';
    Storage::put($path, json_encode(['priority' => 'high', 'category' => 'infra']));
    $ticket->update(['attachment_path' => $path]);

    // executa o job de forma síncrona no teste
    (new ProcessTicketAttachment($ticket))->handle();

    $ticket->refresh();

    expect($ticket->detail->priority)->toBe('high')
        ->and($ticket->detail->category)->toBe('infra')
        ->and($ticket->status)->toBe('done')
        ->and($ticket->detail->processed_at)->not->toBeNull();

    Notification::assertSentTo($user, TicketProcessed::class);
});
