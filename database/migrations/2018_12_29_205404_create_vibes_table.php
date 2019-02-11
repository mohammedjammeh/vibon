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
            $table->string('name');
            $table->text('description');
            $table->boolean('open');
            $table->boolean('auto_dj');
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
        Schema::dropIfExists('vibes');
    }
}
