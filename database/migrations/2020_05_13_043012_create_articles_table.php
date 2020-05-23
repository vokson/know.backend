<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uin');
            $table->integer('version');
            $table->string('subject');
            $table->string('body');
            $table->string('lowered_subject');
            $table->string('lowered_body');

            $table->integer('user_id')
                ->references('id')->on('users')->onDelete('restrict');

            $table->boolean('is_attachment_exist')->default(false);
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
        Schema::dropIfExists('articles');
    }
}
