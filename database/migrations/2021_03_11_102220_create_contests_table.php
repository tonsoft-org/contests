<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contests', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->decimal('balance');
            $table->string('title');
            $table->string('hash');
            $table->integer('last_paid')->unsigned();
            $table->string('link');
            $table->smallInteger('max_voting_attempts')->unsigned();
            $table->string('jury_id');
            $table->integer('start_at')->unsigned();
            $table->integer('end_at')->unsigned();
            $table->integer('voting_end_at')->unsigned();
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
        Schema::dropIfExists('contests');
    }
}
