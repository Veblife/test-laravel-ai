<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketSentiment: string
{
    case Positive = 'Positive';
    case Neutral = 'Neutral';
    case Negative = 'Negative';
}
