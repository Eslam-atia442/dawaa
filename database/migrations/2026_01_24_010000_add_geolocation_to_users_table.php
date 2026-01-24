<?php

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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('lat', 10, 7)->comment('Latitude');
            $table->decimal('long', 10, 7)->comment('Longitude');
            $table->text('map_description')->comment('Map description');
            $table->text('note')->nullable()->comment('Optional note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['lat', 'long', 'map_description', 'note']);
        });
    }
};
