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
        Schema::create('exports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model'); // The model being exported (e.g., 'GoldFund')
            $table->enum('status', ['processing', 'ready', 'failed'])->default('processing');
            $table->string('file_path')->nullable();
            $table->foreignId('user_id')->constrained('admins')->onDelete('cascade');
            $table->json('parameters')->nullable(); // Store filters/parameters as JSON
            $table->unsignedBigInteger('total_records')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exports');
    }
};
