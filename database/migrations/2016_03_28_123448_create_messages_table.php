<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('send_to')->unsigned();
            $table->foreign('send_to')->references('id')->on('users')->onDelete('cascade');

            $table->integer('send_from')->unsigned();
            $table->foreign('send_from')->references('id')->on('users')->onDelete('cascade');

            $table->text('message');

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
        //Schema::drop('messages');
    }
}
