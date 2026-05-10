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
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('transaction_id');
            $table->unsignedBigInteger('seller_id');
            $table->foreign('seller_id')->references('user_id')->on('users');
            $table->unsignedBigInteger('buyer_id');
            $table->foreign('buyer_id')->references('user_id')->on('users');
            $table->enum('status', ["pending", "accepted", "rejected"])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
