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
        Schema::create('licencables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licence_id');
            $table->foreignId('licencable_id');
            $table->string('licencable_type');
            $table->foreign('licence_id')
                ->references('id')
                ->on('licences');
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
        Schema::dropIfExists('licencables');
    }
};
