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
        Schema::create('asset_components', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('asset_id')->references('id')->on('assets')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('asset_component_category_id')->references('id')->on('asset_component_categories')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('manufacturer_id')->nullable()->references('id')->on('manufacturers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name', 128);
            $table->string('serial', 128)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_components');
    }
};
