<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTheMultiplePrimaryKeysOnTrackVibeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('track_vibe', function (Blueprint $table) {
            $table->dropForeign('track_vibe_track_id_foreign');
            $table->dropForeign('track_vibe_vibe_id_foreign');

            $table->dropPrimary(['track_id', 'vibe_id', 'auto_related']);
            $table->unique(['track_id', 'vibe_id', 'auto_related']);

            $table->foreign('track_id')->references('id')->on('tracks')->onDelete('cascade');
            $table->foreign('vibe_id')->references('id')->on('vibes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('track_vibe', function (Blueprint $table) {
            $table->primary(['track_id', 'vibe_id', 'auto_related']);
            $table->dropUnique(['track_id', 'vibe_id', 'auto_related']);
        });
    }
}
