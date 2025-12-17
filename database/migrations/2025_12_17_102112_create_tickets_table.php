<?php

declare(strict_types=1);

use App\Enums\TicketStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description');

            // Enum-status: Open/Resolved
            $table->string('status')->default(TicketStatus::Open->value)->index();

            $table->string('category')->nullable()->index(); // Technical/Billing/General и т.п.
            $table->string('sentiment')->nullable()->index(); // Positive/Neutral/Negative
            $table->text('suggested_reply')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
