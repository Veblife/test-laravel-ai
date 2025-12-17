<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TicketCategory;
use App\Enums\TicketSentiment;
use App\Models\Ticket;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

final class AiTicketEnricher
{
    public function __construct(
        private readonly ClientInterface $http,
    ) {
    }

    /**
     * Expects AI to return:
     * { "category": "...", "sentiment": "...", "reply": "..." }
     * @throws GuzzleException
     */
    public function enrichTicketById(int $ticketId): void
    {
        $ticket = Ticket::query()->find($ticketId);

        if (! $ticket) {
            return;
        }

        $aiUrl = (string) config('services.ai.url');
        $aiKey = (string) config('services.ai.key');

        if ($aiUrl === '' || $aiKey === '') {
            Log::warning('AI enrichment skipped: services.ai.url or services.ai.key not configured', [
                'ticket_id' => $ticketId,
            ]);
            return;
        }

        $response = $this->http->request('POST', $aiUrl, [
            'headers' => [
                'Authorization' => 'Bearer '.$aiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'description' => $ticket->description,
            ],
            'timeout' => 20,
        ]);

        $payload = json_decode((string) $response->getBody(), true);

        if (! is_array($payload)) {
            throw new \RuntimeException('AI response is not valid JSON');
        }

        $categoryRaw = Arr::get($payload, 'category');
        $sentimentRaw = Arr::get($payload, 'sentiment');
        $replyRaw = Arr::get($payload, 'reply');

        $category = TicketCategory::tryFrom(is_string($categoryRaw) ? $categoryRaw : '') ?? TicketCategory::General;
        $sentiment = TicketSentiment::tryFrom(is_string($sentimentRaw) ? $sentimentRaw : '') ?? TicketSentiment::Neutral;
        $reply = is_string($replyRaw) ? $replyRaw : null;

        $ticket->forceFill([
            'category' => $category,
            'sentiment' => $sentiment,
            'suggested_reply' => $reply,
        ])->save();
    }
}
