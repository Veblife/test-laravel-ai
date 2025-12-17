<?php

declare(strict_types=1);

namespace App\Services\Ai\Dto;

use App\Enums\TicketCategory;
use App\Enums\TicketSentiment;

final class AiEnrichmentResult
{
    public function __construct(
        public readonly TicketCategory $category,
        public readonly TicketSentiment $sentiment,
        public readonly ?string $reply,
        public readonly array $raw = [],
    ) {
    }
}
