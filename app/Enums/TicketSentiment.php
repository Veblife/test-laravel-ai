<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketSentiment: string
{
    case positive = 'positive';
    case neutral = 'neutral';
    case negative = 'negative';
}
