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
        Schema::create('asset_files', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('asset_id')->references('id')->on('assets')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('uploader_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name', 512);
            $table->string('extension', 64);
            $table->unsignedBigInteger('size');
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
        Schema::dropIfExists('asset_files');
    }
};
