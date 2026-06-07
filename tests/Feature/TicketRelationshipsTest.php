<?php

use App\Models\Company;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('company tem muitos projects (1:N)', function () {
    $company = Company::factory()
        ->has(\App\Models\Project::factory()->count(3))
        ->create();

    expect($company->projects)->toHaveCount(3);
});

it('project tem muitos tickets (1:N)', function () {
    $project = \App\Models\Project::factory()
        ->has(Ticket::factory()->count(2))
        ->create();

    expect($project->tickets)->toHaveCount(2);
});

it('ticket tem exatamente um detail (1:1)', function () {
    $ticket = Ticket::factory()->create();
    $ticket->detail()->create(['priority' => 'high']);

    expect($ticket->detail)->toBeInstanceOf(TicketDetail::class)
        ->and($ticket->detail->priority)->toBe('high');
});

it('user tem um profile (1:1)', function () {
    $user = User::factory()->create();
    UserProfile::factory()->create(['user_id' => $user->id]);

    expect($user->fresh()->profile)->toBeInstanceOf(UserProfile::class);
});
