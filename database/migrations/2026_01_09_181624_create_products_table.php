<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Store;
use App\Models\Product;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class, 'parent_id')->nullable()->constrained()->onDelete('cascade');
            $table->longText('name')->nullable();
            $table->longText('description')->nullable();
            $table->foreignIdFor(Store::class)->nullable()->constrained()->onDelete('set null');
            $table->foreignIdFor(City::class)->nullable()->constrained()->onDelete('set null');
            $table->foreignIdFor(Category::class)->nullable()->constrained()->onDelete('set null');
            $table->foreignIdFor(Brand::class)->nullable()->constrained()->onDelete('set null');
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('production_line_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
