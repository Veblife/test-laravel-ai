<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TicketCategory;
use App\Enums\TicketSentiment;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'category',
        'sentiment',
        'suggested_reply',
    ];

    protected $casts = [
        'status' => TicketStatus::class,
        'category' => TicketCategory::class,
        'sentiment' => TicketSentiment::class,
    ];
}
