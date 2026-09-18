<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimelogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timelogs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('card_id');
            $table->unsignedInteger('action_id');
            $table->unsignedInteger('device_id');
            $table->unsignedInteger('user_id');
            $table->datetime('time');
            $table->timestamps();

            $table->foreign('card_id')
                ->references('id')
                ->on('cards');
            $table->foreign('action_id')
                ->references('id')
                ->on('actions');
            $table->foreign('device_id')
                ->references('id')
                ->on('devices');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timelogs');
    }
}
