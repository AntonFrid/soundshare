<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAudioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('title')->default('');
            $table->string('description')->default('');
            $table->integer('likes')->default(0);
            $table->integer('streams')->default(0);
            $table->boolean('private')->default(false);
            $table->boolean('download')->default(false);
            $table->string('waveform_path')->nullable();
            $table->string('file_path');
            $table->string('uuid')->default('');
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
        Schema::dropIfExists('audio');
    }
}
