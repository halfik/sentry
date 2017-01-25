<?php

use  Netinteractive\Elegant\Db\Schema\Blueprint;
use  Netinteractive\Elegant\Db\Migrations\Migration;

/**
 * Class AddMissingRelations
 */
class AddMissingRelations extends Migration
{

    /**
     * @throws Exception
     */
    public function up()
    {
        DB::beginTransaction();

        $userRoleTable = \Config::get('packages.netinteractive.sentry.config.user_role_pivot_table');

        try {
            Schema::table($userRoleTable, function($table)
            {
                $userTable = \Config::get('packages.netinteractive.sentry.config.user_table');
                $roleTable = \Config::get('packages.netinteractive.sentry.config.role_table');

                $table->foreign('user__id')->references('id')->on($userTable)->onDelete('cascade');
                $table->foreign('role__id')->references('id')->on($roleTable)->onDelete('cascade');
            });
        }
        catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
