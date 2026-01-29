<?php

use App\Models\Admin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            // transaction amount (positive value)
            $table->decimal('amount', 15, 2);
            // 1 => credit, 2 => debit (see WalletTransactionTypeEnum)
            $table->tinyInteger('type');
            // snapshot of wallet balance before and after this transaction
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            // polymorphic-style reference to the source of this transaction (order, refund, admin, subscription, etc.)
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description')->nullable();
            $table->foreignIdFor(Admin::class)->nullable()->constrained()->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
