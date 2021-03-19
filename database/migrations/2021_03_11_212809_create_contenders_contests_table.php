<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContendersContestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contenders_contests', function (Blueprint $table) {
            $table->string('contender_id');
            $table->string('contest_id');
            $table->timestamps();

            $table->primary(['contender_id', 'contest_id']);

            $table->foreign('contender_id')->references('id')->on('contenders');
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
        Schema::dropIfExists('contenders_contests');
    }
}
