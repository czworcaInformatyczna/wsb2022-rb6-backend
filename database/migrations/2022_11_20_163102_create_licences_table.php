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
        Schema::create('licences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturer_id');
            $table->foreignId('category_id');
            $table->string('product_key');
            $table->string('email');
            $table->date('expiration_date');
            $table->boolean('reassignable');
            $table->integer('slots');
            $table->foreign('manufacturer_id')
                ->references('id')
                ->on('manufacturers');
            $table->foreign('category_id')
                ->references('id')
                ->on('licence_categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('licences');
    }
};
