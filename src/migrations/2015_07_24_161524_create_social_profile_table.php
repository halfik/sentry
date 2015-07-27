<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialProfileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('social_profile', function($table)
        {
            $table->increments('id');
            $table->integer('user__id')->unsigned();
            $table->string('profile_id');
            $table->string('type');
            $table->timestamps();
            $table->unique( array('user__id', 'type') );

            $table->foreign('user__id')->references('id')->on('user')->onDelete('cascade');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        if (Schema::hasTable('social_profile')) {
            Schema::drop('social_profile');
        }
	}

}
