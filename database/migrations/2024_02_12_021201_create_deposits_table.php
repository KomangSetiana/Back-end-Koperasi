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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->date('date');
            $table->integer('debet')->nullable();
            $table->integer('kredit')->nullable();
            $table->integer('saldo');
            $table->integer('interest')->nullable();
            $table->unsignedBigInteger('deposit_type_id');
            $table->unsignedBigInteger('created_by');
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('deposit_type_id')->references('id')->on('deposit_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
