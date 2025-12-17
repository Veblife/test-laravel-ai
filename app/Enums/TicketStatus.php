<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'Open';
    case Resolved = 'Resolved';
}
