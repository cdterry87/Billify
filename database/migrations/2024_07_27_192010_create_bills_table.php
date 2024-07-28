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
            $table->boolean('january')->nullable();
            $table->boolean('february')->nullable();
            $table->boolean('march')->nullable();
            $table->boolean('april')->nullable();
            $table->boolean('may')->nullable();
            $table->boolean('june')->nullable();
            $table->boolean('july')->nullable();
            $table->boolean('august')->nullable();
            $table->boolean('september')->nullable();
            $table->boolean('october')->nullable();
            $table->boolean('november')->nullable();
            $table->boolean('december')->nullable();
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
