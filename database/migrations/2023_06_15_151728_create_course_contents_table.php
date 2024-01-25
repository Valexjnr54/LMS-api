<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_contents', function (Blueprint $table) {
            $table->id();
            $table->integer('lecturer_id');
            $table->integer('course_id');
            $table->string('topic');
            $table->string('completion_method');
            $table->string('question')->nullable();
            $table->json('options')->nullable();
            $table->string('answer')->nullable();
            $table->string('type');
            $table->text('content')->nullable();
            $table->string('content_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('audio_url')->nullable();
            $table->string('file_url')->nullable();
            $table->string('iframe')->nullable();
            $table->string('status')->default('active');
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
        Schema::dropIfExists('course_contents');
    }
};
