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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('reg_number')->nullable()->unique();
            $table->integer('level')->nullable();
            $table->string('dept')->nullable();
            $table->string('faculty')->nullable();
            $table->string('phone');
            $table->string('gender')->nullable();
            $table->integer('role');
            $table->string('user_type');
            $table->integer('points')->default(0);
            $table->integer('credits')->default(0);
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('password');
            $table->string('status')->default('active');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
