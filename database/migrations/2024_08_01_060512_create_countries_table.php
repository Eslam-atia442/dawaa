<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table){
            $table->id();
            $table->string('name')->nullable();
            $table->string('key', 100)->default('+965');
            $table->string('currency', 100)->nullable();
            $table->string('currency_code', 100)->nullable();
            $table->string('iso2', 100)->nullable();
            $table->string('iso3', 100)->nullable();
            $table->string('flag', 100)->nullable();
            $table->boolean('is_active')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
