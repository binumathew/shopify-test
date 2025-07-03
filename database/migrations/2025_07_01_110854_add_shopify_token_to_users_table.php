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
        Schema::table('users', function (Blueprint $table) {
        $table->string('shopify_domain')->unique()->nullable();
        $table->string('access_token')->nullable();
        $table->integer('inventory_threshold')->default(10); // default threshold of 10
        $table->string('alert_email')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'shopify_domain',
                'access_token',
                'inventory_threshold',
                'alert_email',
            ]);
        });
    }
};
