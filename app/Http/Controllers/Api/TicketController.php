<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Jobs\EnrichTicketWithAi;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;

final class TicketController extends Controller
{
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = Ticket::query()->create([
            'title' => $request->string('title')->toString(),
            'description' => $request->string('description')->toString(),
            'status' => TicketStatus::open,
        ]);

        EnrichTicketWithAi::dispatch($ticket->id);

        return response()->json([
            'data' => $this->serializeTicket($ticket),
            'enrichment' => 'queued',
        ], 201);
    }

    public function show(Ticket $ticket): JsonResponse
    {
        return response()->json([
            'data' => $this->serializeTicket($ticket),
        ]);
    }

    private function serializeTicket(Ticket $ticket): array
    {
        return [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'status' => $ticket->status?->value,
            'category' => $ticket->category?->value,
            'sentiment' => $ticket->sentiment?->value,
            'suggested_reply' => $ticket->suggested_reply,
            'created_at' => $ticket->created_at,
            'updated_at' => $ticket->updated_at,
        ];
    }
}
