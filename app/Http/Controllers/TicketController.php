<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Jobs\ProcessTicketAttachment;
use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['project', 'detail', 'user'])
            ->latest()
            ->get();

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
        ]);
    }

    public function create()
    {
        return Inertia::render('Tickets/Create', [
            'projects' => Project::select('id', 'name')->get(),
        ]);
    }

    public function store(StoreTicketRequest $request)
    {
        $data = $request->validated();

        // Salva o anexo, se enviado (vai para storage/app/private/attachments)
        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments');
        }

        $ticket = Ticket::create([
            'project_id' => $data['project_id'],
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $path ? 'processing' : 'open',
            'attachment_path' => $path,
        ]);

        $ticket->detail()->create([]);

        if ($path) {
            ProcessTicketAttachment::dispatch($ticket);
        }

         return redirect()->route('tickets.index')
            ->with('success', 'Ticket criado com sucesso.');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['project', 'detail', 'user']);

        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket,
        ]);
    }

    public function destroy(Ticket $ticket)
    {
        if ($ticket->attachment_path) {
            Storage::delete($ticket->attachment_path);
        }

        // o detail é apagado em cascata
        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket excluído.');
    }
}
