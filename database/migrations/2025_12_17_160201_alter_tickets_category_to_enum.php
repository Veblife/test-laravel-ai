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
        // Normalize existing values to the new enum set (lowercase). Keep nulls as-is.
        DB::table('tickets')->whereIn('category', ['Technical', 'technical'])
            ->update(['category' => 'technical']);
        DB::table('tickets')->whereIn('category', ['Billing', 'billing'])
            ->update(['category' => 'billing']);
        DB::table('tickets')->whereIn('category', ['General', 'general'])
            ->update(['category' => 'general']);

        // Any other non-null values that are not in the allowed set should be set to null
        DB::table('tickets')
            ->whereNotNull('category')
            ->whereNotIn('category', ['technical', 'billing', 'general'])
            ->update(['category' => null]);

        // Change the column type to enum with default null
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('category', ['technical', 'billing', 'general'])
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
        // Revert the column back to string (nullable)
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('category')->nullable()->change();
        });

        // Optionally normalize values back to Title Case for consistency with original enum class
        DB::table('tickets')->where('category', 'technical')->update(['category' => 'Technical']);
        DB::table('tickets')->where('category', 'billing')->update(['category' => 'Billing']);
        DB::table('tickets')->where('category', 'general')->update(['category' => 'General']);
    }
};
