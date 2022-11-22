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
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('asset_id')->references('id')->on('assets')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title', 300);
            $table->string('maintenance_type', 64);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_maintenances');
    }
};
