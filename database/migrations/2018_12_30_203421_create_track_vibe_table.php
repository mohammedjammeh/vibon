<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackVibeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('track_vibe', function (Blueprint $table) {
            $table->primary(['track_id', 'vibe_id']);
            $table->unsignedInteger('track_id');
            $table->unsignedInteger('vibe_id');

            $table->foreign('track_id')->references('id')->on('tracks')->onDelete('cascade');
            $table->foreign('vibe_id')->references('id')->on('vibes')->onDelete('cascade');

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
        Schema::dropIfExists('track_vibe');
    }
}