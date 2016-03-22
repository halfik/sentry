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
        $tableName =  \Config::get('netinteractive.sentry.social_profile_table');

        Schema::create($tableName, function($table)
        {
            $table->increments('id');
            $table->integer('user__id')->unsigned();
            $table->string('profile_id');
            $table->string('type');
            $table->timestamps();
            $table->unique( array('user__id', 'type') );

            $userTableName = \Config::get('netinteractive.sentry.user_table');

            $table->foreign('user__id')->references('id')->on($userTableName)->onDelete('cascade');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        $tableName =  \Config::get('netinteractive.sentry.social_profile_table');

        if (Schema::hasTable($tableName)) {
            Schema::drop($tableName);
        }
	}

}
