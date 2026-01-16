<?php

use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Enums\PaymentTypeEnum;
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
        Schema::dropIfExists('transactions');
        
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relationship - can belong to Order, Wallet, etc.
            $table->morphs('transactionable');
            
            // Who initiated the transaction (User, Admin, etc.)
            $table->nullableMorphs('initiated_by');
            
            // Who processed the transaction (Admin, System, etc.)
            $table->nullableMorphs('processed_by');
            
            // Transaction details
            $table->tinyInteger('type')->default(TransactionTypeEnum::WALLET_DEPOSIT->value);
            $table->tinyInteger('status')->default(TransactionStatusEnum::PENDING->value);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('egp');
            
            // Payment method details
            $table->tinyInteger('payment_method')->default(PaymentTypeEnum::ONLINE->value)->nullable(); // Can use PaymentTypeEnum
            $table->string('payment_reference')->nullable(); // External payment gateway reference
            $table->string('transaction_reference')->unique()->nullable(); // Internal reference number
            
            // Additional information
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // For storing additional data
            
            // Timestamps
            $table->timestamp('processed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes for performance

            $table->index('transaction_reference');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
