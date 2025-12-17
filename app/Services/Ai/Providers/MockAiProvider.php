<?php

declare(strict_types=1);

namespace App\Services\Ai\Providers;

use App\Enums\TicketCategory;
use App\Enums\TicketSentiment;
use App\Services\Ai\AiProvider;
use App\Services\Ai\Dto\AiEnrichmentResult;

final class MockAiProvider implements AiProvider
{
    public function enrichTicket(string $description): AiEnrichmentResult
    {
        $categoryRaw = (string) config('services.ai.mock.category', 'General');
        $sentimentRaw = (string) config('services.ai.mock.sentiment', 'Neutral');
        $reply = (string) config('services.ai.mock.reply', 'Mock reply: ticket received, we will help you shortly.');

        $category = TicketCategory::tryFrom($categoryRaw) ?? TicketCategory::general;
        $sentiment = TicketSentiment::tryFrom($sentimentRaw) ?? TicketSentiment::neutral;

        return new AiEnrichmentResult(
            category: $category,
            sentiment: $sentiment,
            reply: $reply,
            raw: [
                'mock' => true,
                'description_preview' => mb_substr($description, 0, 200),
            ],
        );
    }
}
