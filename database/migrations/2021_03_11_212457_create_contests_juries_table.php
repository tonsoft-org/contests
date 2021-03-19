<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContestsJuriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contests_juries', function (Blueprint $table) {
            $table->string('contest_id');
            $table->string('jury_id');
            $table->timestamps();

            $table->primary(['contest_id', 'jury_id']);

            $table->foreign('contest_id')->references('id')->on('contests')->onDelete('cascade');
            $table->foreign('jury_id')->references('id')->on('juries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contests_juries');
    }
}
