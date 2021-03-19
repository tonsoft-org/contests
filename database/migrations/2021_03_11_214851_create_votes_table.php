<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->string('contest_id');
            $table->integer('submission_id')->unsigned();
            $table->string('jury_id');
            $table->tinyInteger('type');
            $table->string('type_text');
            $table->tinyInteger('mark')->unsigned();
            $table->text('comment');
            $table->timestamps();

            $table->primary(['contest_id', 'submission_id', 'jury_id']);
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
        Schema::dropIfExists('votes');
    }
}
