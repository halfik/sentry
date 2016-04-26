<?php
use Illuminate\Database\Migrations\Migration;


class MigrationNiSentryInstallThrottle extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        $tableName =  \Config::get('packages.netinteractive.sentry.config.throttle_table');

		Schema::create($tableName, function($table)
		{
			$table->increments('id');
			$table->integer('user__id')->unsigned()->nullable();
			$table->string('ip_address')->nullable();
			$table->integer('attempts')->default(0);
			$table->boolean('suspended')->default(0);
			$table->boolean('banned')->default(0);
			$table->timestamp('last_attempt_at')->nullable();
			$table->timestamp('suspended_at')->nullable();
			$table->timestamp('banned_at')->nullable();

			// We'll need to ensure that MySQL uses the InnoDB engine to
			// support the indexes, other engines aren't affected.
			$table->engine = 'InnoDB';
			$table->index('user__id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        $tableName =  \Config::get('packages.netinteractive.sentry.config.throttle_table');

		Schema::drop($tableName);
	}

}
