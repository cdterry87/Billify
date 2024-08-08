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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->string('name', 64);
            $table->string('category', 64)->nullable();
            $table->string('description')->nullable();
            $table->integer('amount')->length(9);
            $table->integer('day')->length(2);
            $table->boolean('january')->default(false)->nullable();
            $table->boolean('february')->default(false)->nullable();
            $table->boolean('march')->default(false)->nullable();
            $table->boolean('april')->default(false)->nullable();
            $table->boolean('may')->default(false)->nullable();
            $table->boolean('june')->default(false)->nullable();
            $table->boolean('july')->default(false)->nullable();
            $table->boolean('august')->default(false)->nullable();
            $table->boolean('september')->default(false)->nullable();
            $table->boolean('october')->default(false)->nullable();
            $table->boolean('november')->default(false)->nullable();
            $table->boolean('december')->default(false)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
