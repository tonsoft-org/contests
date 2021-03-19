<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->string('contest_id');

            $table->string('address');
            $table->string('contact');
            $table->integer('applied_at')->unsigned();
            $table->text('file_link');
            $table->text('forum_link');
            $table->string('hash');

            $table->integer('accepted')->unsigned();
            $table->integer('abstained')->unsigned();
            $table->integer('rejected')->unsigned();
            $table->float('avg_mark')->unsigned();
            $table->integer('total_mark')->unsigned();

            $table->timestamps();

            $table->primary(['id', 'contest_id']);
            $table->foreign('contest_id')->references('id')->on('contests')->onDelete('cascade');
            $table->foreign('address')->references('id')->on('contenders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submissions');
    }
}
