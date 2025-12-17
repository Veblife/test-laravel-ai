<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Services\Ai\Dto\AiEnrichmentResult;

interface AiProvider
{
    public function enrichTicket(string $description): AiEnrichmentResult;
}
