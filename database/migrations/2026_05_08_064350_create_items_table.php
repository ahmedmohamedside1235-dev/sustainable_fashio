<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('item_id');
            $table->unsignedBigInteger('seller_id');
            $table->foreign('seller_id')->references('user_id')->on('users');
            $table->unsignedBigInteger('material_id');
            $table->foreign('material_id')->references('material_id')->on('materials');
            $table->unsignedBigInteger('condition_id');
            $table->foreign('condition_id')->references('condition_id')->on('item_conditions');
            $table->double('price');
            $table->string('image', 255);
            $table->timestamps();   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
