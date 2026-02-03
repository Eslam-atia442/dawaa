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
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('original_price', 10, 2)->nullable()->after('price')->comment('Price before discount');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('original_price')->comment('Discount amount per unit');
            $table->decimal('discounted_price', 10, 2)->nullable()->after('discount_amount')->comment('Price after discount per unit');
            $table->decimal('total_discount', 10, 2)->default(0)->after('total_price')->comment('Total discount for this item (discount_amount * quantity)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'discount_amount', 'discounted_price', 'total_discount']);
        });
    }
};
