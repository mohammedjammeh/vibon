<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrimaryKeyIdColumnToTrackVibeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('track_vibe', function (Blueprint $table) {
            $table->increments('id')->first();
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
            $table->dropPrimary('id');
            $table->dropColumn('id');
        });
    }
}
