<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('shopify_domain')->unique();
            $table->string('shopify_token')->nullable();
            $table->boolean('shopify_grandfathered')->default(false);
            $table->boolean('is_namespaced')->default(false); // renamed to avoid confusion
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shops');
    }
};
