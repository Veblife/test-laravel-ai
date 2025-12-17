<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Services\Ai\AiProvider;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

final class AiTicketEnricher
{
    public function __construct(
        private readonly AiProvider $provider,
    ) {
    }

    /**
     * @throws GuzzleException
     */
    public function enrichTicketById(int $ticketId): void
    {
        $ticket = Ticket::query()->find($ticketId);

        if (! $ticket) {
            return;
        }

        try {
            $result = $this->provider->enrichTicket((string) $ticket->description);
        } catch (\Throwable $e) {
            Log::warning('AI enrichment failed', [
                'ticket_id' => $ticketId,
                'error' => $e->getMessage(),
            ]);
            return;
        }

        $ticket->forceFill([
            'category' => $result->category,
            'sentiment' => $result->sentiment,
            'suggested_reply' => $result->reply,
            'status' => TicketStatus::resolved,
        ])->save();
    }
}
