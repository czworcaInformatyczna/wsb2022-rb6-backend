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
        Schema::dropIfExists('cookies');
        Schema::create('cookies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('language_id');
            $table->foreignId('theme_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('language_id')
                ->references('id')
                ->on('languages');
            $table->foreign('theme_id')
                ->references('id')
                ->on('themes');
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
        Schema::dropIfExists('cookies');
    }
};
