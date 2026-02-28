<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Country;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contact_us', function (Blueprint $table) {
            $table->id();
            $table->longText('name');
            $table->longText('email');
            $table->longText('message');
            $table->foreignIdFor(Country::class)->nullable()->constrained()->onDelete('set null');
            $table->string('phone')->nullable();
            // $table->boolean('is_active')->default(true);
     //     $table->foreignIdFor(Model::class)->nullable()->constrained()->onDelete('set null'); // example
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_us');
    }
};
