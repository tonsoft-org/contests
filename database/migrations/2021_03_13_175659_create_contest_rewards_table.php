<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContestRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contest_rewards', function (Blueprint $table) {
            $table->string('contest_id');
            $table->integer('rating')->unsigned();
            $table->integer('reward')->unsigned();
            $table->string('transaction')->nullable();
            $table->timestamps();

            $table->primary(['contest_id', 'rating']);

            $table->foreign('contest_id')->references('id')->on('contests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contest_rewards');
    }
}
