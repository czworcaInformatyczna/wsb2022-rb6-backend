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
        Schema::create('licence_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licence_id')
                ->references('id')
                ->on('licences')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('uploader_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('name', 512);
            $table->string('extension', 64);
            $table->unsignedBigInteger('size');
            $table->text('notes')
                ->nullable();
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
        Schema::dropIfExists('licence_files');
    }
};
