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
        Schema::table('users', function (Blueprint $table){
            $table->foreignIdFor(\App\Models\Country::class)->after('id')->nullable()->constrained()->onDelete('set null');

        });
    }

    /**
     *
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table){
            $table->dropForeign('users_country_id_foreign');
            $table->dropColumn('country_id');
        });
    }
};
