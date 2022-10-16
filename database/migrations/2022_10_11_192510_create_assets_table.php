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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name', 256);
            $table->string('tag', 32)->unique();
            $table->foreignId('asset_model_id')->references('id')->on('asset_models')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('image', 256)->nullable();
            $table->string('serial', 256);
            $table->smallInteger('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assets');
    }
};
