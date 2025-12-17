<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketCategory: string
{
    case technical = 'technical';
    case billing = 'billing';
    case general = 'general';
}
