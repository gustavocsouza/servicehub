<?php

use App\Jobs\ProcessTicketAttachment;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('exige autenticação para listar tickets', function () {
    $this->get('/tickets')->assertRedirect('/login');
});

it('usuário autenticado cria um ticket', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->post('/tickets', [
            'project_id' => $project->id,
            'title' => 'Ticket de teste',
            'description' => 'descrição',
        ])
        ->assertRedirect('/tickets');

    $this->assertDatabaseHas('tickets', ['title' => 'Ticket de teste']);
});

it('dispara o job quando há anexo', function () {
    Queue::fake();
    Storage::fake();

    $user = User::factory()->create();
    $project = Project::factory()->create();

    $file = UploadedFile::fake()->createWithContent(
        'dados.json',
        json_encode(['priority' => 'low'])
    );

    $this->actingAs($user)->post('/tickets', [
        'project_id' => $project->id,
        'title' => 'Com anexo',
        'attachment' => $file,
    ]);

    Queue::assertPushed(ProcessTicketAttachment::class);
});

it('deleta o ticket e redireciona', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->delete("/tickets/{$ticket->id}")
        ->assertRedirect('/tickets');

    $this->assertModelMissing($ticket);
});
