<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_models', function (Blueprint $table) {
            $table->id();
            $table->string('name', 256);
            $table->timestamps();
            // Category
            $table->foreignId('asset_category_id')->references('id')->on('asset_categories')->cascadeOnUpdate()->cascadeOnDelete();
            // Manufacturer
            $table->foreignId('asset_manufacturer_id')->references('id')->on('asset_manufacturers')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_models');
    }
};
