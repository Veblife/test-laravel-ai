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
        // Normalize existing values to the new enum set (lowercase)
        DB::table('tickets')->where('status', 'Open')->update(['status' => 'open']);
        DB::table('tickets')->where('status', 'Resolved')->update(['status' => 'resolved']);
        DB::table('tickets')->whereNull('status')->update(['status' => 'open']);

        // Change the column type to enum with default 'open'
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('status', ['open', 'resolved'])->default('open')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the column back to string. Keep default consistent with the original creation migration ('Open').
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('status')->default('Open')->change();
        });

        // Optionally normalize values back to title case for consistency
        DB::table('tickets')->where('status', 'open')->update(['status' => 'Open']);
        DB::table('tickets')->where('status', 'resolved')->update(['status' => 'Resolved']);
    }
};
