<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('amount', 15);
            $table->string('day', 2);
            $table->boolean('month')->nullable();
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bills');
    }
}
