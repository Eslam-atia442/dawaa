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
        Schema::table('users', function (Blueprint $table) {
            $table->longText('social_token')->nullable()->after('password');
            $table->enum('social_type', ['facebook', 'google', 'apple'])->nullable()->after('social_token');
            $table->string('social_id')->nullable()->after('social_type');
            $table->string('social_email')->nullable()->after(column: 'social_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['social_token', 'social_type', 'social_id', 'social_email']);
        });
    }
};
