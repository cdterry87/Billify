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
            $table->text('description');
            $table->string('amount', 15);
            $table->string('day', 2);
            $table->boolean('month_january');
            $table->boolean('month_february');
            $table->boolean('month_march');
            $table->boolean('month_april');
            $table->boolean('month_may');
            $table->boolean('month_june');
            $table->boolean('month_july');
            $table->boolean('month_august');
            $table->boolean('month_september');
            $table->boolean('month_october');
            $table->boolean('month_november');
            $table->boolean('month_december');
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
