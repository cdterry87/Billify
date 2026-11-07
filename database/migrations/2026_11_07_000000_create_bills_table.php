<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 64);
            $table->string('category', 32);
            $table->string('description')->nullable();
            $table->unsignedBigInteger('amount_cents');
            $table->unsignedTinyInteger('due_day');
            $table->json('months')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'due_day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
