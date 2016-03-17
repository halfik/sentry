<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersParametersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('users_parameters')){
            Schema::create('users_parameters', function(Blueprint $table)
            {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('name', 255);
                $table->longText('value');
                $table->unique( array('user_id', 'name') );
            });
        }

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('users_parameters');
	}

}
