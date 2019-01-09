<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserVibeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_vibe', function (Blueprint $table) {
            $table->primary(['user_id', 'vibe_id', 'owner']);

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('vibe_id');
            $table->boolean('owner')->default(0);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('user_vibe');
    }
}
