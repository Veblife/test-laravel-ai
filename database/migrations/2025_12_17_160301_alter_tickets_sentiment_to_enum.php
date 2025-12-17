<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalize existing values to the new enum set (lowercase) and null out unexpected values
        DB::table('tickets')->where('sentiment', 'Positive')->update(['sentiment' => 'positive']);
        DB::table('tickets')->where('sentiment', 'positive')->update(['sentiment' => 'positive']);
        DB::table('tickets')->where('sentiment', 'Neutral')->update(['sentiment' => 'neutral']);
        DB::table('tickets')->where('sentiment', 'neutral')->update(['sentiment' => 'neutral']);
        DB::table('tickets')->where('sentiment', 'Negative')->update(['sentiment' => 'negative']);
        DB::table('tickets')->where('sentiment', 'negative')->update(['sentiment' => 'negative']);

        // Set any other non-null values to null to satisfy the enum constraint
        DB::table('tickets')
            ->whereNotNull('sentiment')
            ->whereNotIn('sentiment', ['positive', 'neutral', 'negative'])
            ->update(['sentiment' => null]);

        // Change the column type to enum with default null
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('sentiment', ['positive', 'neutral', 'negative'])
                ->nullable()
                ->default(null)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the column back to string, keeping it nullable
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('sentiment')->nullable()->change();
        });

        // Optionally normalize values back to Title Case for consistency with earlier string usage
        DB::table('tickets')->where('sentiment', 'positive')->update(['sentiment' => 'Positive']);
        DB::table('tickets')->where('sentiment', 'neutral')->update(['sentiment' => 'Neutral']);
        DB::table('tickets')->where('sentiment', 'negative')->update(['sentiment' => 'Negative']);
    }
};
