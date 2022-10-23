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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('notes', 1024)->nullable();
            $table->integer('warranty', false, true)->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('order_number', 256)->nullable();
            $table->decimal('price', 9, 3, false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('notes');
            $table->dropColumn('warranty');
            $table->dropColumn('purchase_date');
            $table->dropColumn('order_number');
            $table->dropColumn('price');
        });
    }
};
