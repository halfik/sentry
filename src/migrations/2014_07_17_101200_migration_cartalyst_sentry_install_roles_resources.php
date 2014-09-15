<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentryInstallRolesResources extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('roles_resources')){
            Schema::create('roles_resources', function($table)
            {
                $table->increments('id');
                $table->integer('role_id')->unsigned()->nullable();
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

                $table->integer('resource_id')->unsigned()->nullable();
                $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
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
        if (Schema::hasTable('roles_resources')){
            Schema::drop('roles_resources');
        }

    }

}
