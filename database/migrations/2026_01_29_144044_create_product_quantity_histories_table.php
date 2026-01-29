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
        Schema::create('product_quantity_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_change');
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->enum('type', ['credit', 'debit']); // credit = increase quantity, debit = decrease quantity
            $table->enum('reason', ['buy', 'refund', 'order', 'adjustment', 'return']); // reason for the change
            $table->string('reference_type')->nullable(); // order, refund, etc.
            $table->unsignedBigInteger('reference_id')->nullable(); // order id, refund id, etc.
            $table->text('notes')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_quantity_histories');
    }
};
