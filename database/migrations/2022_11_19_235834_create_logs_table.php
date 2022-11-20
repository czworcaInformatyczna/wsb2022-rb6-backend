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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // User who initiated action
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();

            // Item that was directly affected
            $table->string('item_type', 256)->nullable();
            $table->bigInteger('item_id')->nullable();

            // Item that was indirectly affected
            $table->string('target_type', 256)->nullable();
            $table->bigInteger('target_id')->nullable();

            // What was logged
            $table->string('action_type', 256);

            $table->string('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
};
