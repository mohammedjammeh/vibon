<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVibesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vibes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_id');
            $table->text('description');
            $table->boolean('open');
            $table->boolean('auto_dj');
            $table->unsignedInteger('last_played_track_id')->nullable();
            $table->unsignedInteger('last_played_track_progress')->nullable();
            $table->timestamps();

            $table->foreign('last_played_track_id')->references('id')->on('tracks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vibes');
    }
}
