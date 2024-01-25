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
        Schema::create('assigned_group_courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_group_code');
            $table->string('course_id');
            $table->string('course_code');
            $table->string('course_title');
            $table->string('credit_load');
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
        Schema::dropIfExists('assign_group_courses');
    }
};
