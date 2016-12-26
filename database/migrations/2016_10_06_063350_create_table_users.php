<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('email')->unique()->nullable();
            $table->text('avatar_url')->nullable();
//            $table->string('country_code')->default('+86');
            $table->string('phone')->unique()->nullable();
            $table->string('password');

            $table->text('intro')->nullable();
            $table->timestamps();  //创建于、更新于
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
}
