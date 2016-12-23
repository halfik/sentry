<?php

use Netinteractive\Elegant\Migrations\Migration;

/**
 * Class MigrationNiSentryInstallUsers
 */
class MigrationNiSentryInstallUsers extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName =  \Config::get('packages.netinteractive.sentry.config.user_table');

        if (!Schema::hasTable($tableName)){
            Schema::create($tableName, function($table)
            {
                $table->increments('id');
                $table->string('login');
                $table->string('email');
                $table->string('password');
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->text('permissions')->nullable();
                $table->boolean('activated')->default(0);
                $table->string('activation_code')->nullable();
                $table->timestamp('activated_at')->nullable();
                $table->timestamp('last_login')->nullable();
                $table->string('persist_code')->nullable();
                $table->string('reset_password_code')->nullable();
                $table->timestamps();

                // We'll need to ensure that MySQL uses the InnoDB engine to
                // support the indexes, other engines aren't affected.
                $table->engine = 'InnoDB';
                $table->unique('email');
                $table->index('login');
                $table->index('activation_code');
                $table->index('first_name');
                $table->index('last_name');
                $table->index('reset_password_code');
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
        $tableName =  \Config::get('packages.netinteractive.sentry.config.user_table');

        Schema::drop($tableName);
    }

}
