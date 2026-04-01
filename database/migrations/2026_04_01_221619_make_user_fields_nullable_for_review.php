<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('type')->nullable()->default(null)->change();
            $table->decimal('lat', 10, 7)->nullable()->change();
            $table->decimal('long', 10, 7)->nullable()->change();
            $table->text('map_description')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->tinyInteger('type')->nullable(false)->default(\App\Enums\UserTypeEnum::DOCTOR->value)->change();
            $table->decimal('lat', 10, 7)->nullable(false)->change();
            $table->decimal('long', 10, 7)->nullable(false)->change();
            $table->text('map_description')->nullable(false)->change();
        });
    }
};
