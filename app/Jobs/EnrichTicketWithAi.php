<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\AiTicketEnricher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class EnrichTicketWithAi implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $ticketId)
    {
    }

    public function handle(AiTicketEnricher $enricher): void
    {
        $enricher->enrichTicketById($this->ticketId);
    }
}
