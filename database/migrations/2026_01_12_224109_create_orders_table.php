<?php

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\RefundTypeEnum;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained()->onDelete('set null');
            $table->decimal('total_price', 10, 2)->nullable();
            $table->tinyInteger('payment_type')->default(PaymentTypeEnum::ONLINE->value);
            $table->foreignIdFor(Order::class, 'parent_id')->nullable()->constrained()->onDelete('set null'); // refund order
            $table->tinyInteger('refund_type')->default(RefundTypeEnum::CASH->value);


            $table->softDeletes();
            $table->timestamps();
        });
    }
};
