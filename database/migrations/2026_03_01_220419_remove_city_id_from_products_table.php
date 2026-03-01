<?php

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
        if (!Schema::hasColumn('products', 'city_id')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            // Drop foreign key if it exists (name may vary)
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'products'
                AND COLUMN_NAME = 'city_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            foreach ($foreignKeys as $fk) {
                $table->dropForeign($fk->CONSTRAINT_NAME);
            }
            $table->dropColumn('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('set null');
        });
    }
};
