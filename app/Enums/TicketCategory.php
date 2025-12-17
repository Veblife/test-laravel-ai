<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketCategory: string
{
    case Technical = 'Technical';
    case Billing = 'Billing';
    case General = 'General';
}
