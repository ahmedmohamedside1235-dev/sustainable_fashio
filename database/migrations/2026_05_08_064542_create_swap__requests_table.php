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
        Schema::create('swap__requests', function (Blueprint $table) {
            $table->bigIncrements('swap_id');
            $table->unsignedBigInteger('requester_id');
            $table->foreign('requester_id')->references('user_id')->on('users');
            $table->unsignedBigInteger('receiver_id');
            $table->foreign('receiver_id')->references('user_id')->on('users');
            $table->unsignedBigInteger('requested_item_id');
            $table->foreign('requested_item_id')->references('item_id')->on('items');
            $table->enum('status', ["pending", "accepted", "rejected"])->default('pending');
            $table->double('cash_topup_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('swap__requests');
    }
};
