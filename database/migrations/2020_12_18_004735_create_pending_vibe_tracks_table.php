<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingVibeTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_vibe_tracks', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('track_id');
            $table->unsignedInteger('vibe_id');
            $table->unsignedInteger('user_id');
            $table->boolean('attach');

            $table->foreign('track_id')->references('id')->on('tracks')->onDelete('cascade');
            $table->foreign('vibe_id')->references('id')->on('vibes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['track_id', 'vibe_id']);

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
        Schema::dropIfExists('pending_vibe_tracks');
    }
}
