<?php

use  Netinteractive\Elegant\Db\Schema\Blueprint;
use  Netinteractive\Elegant\Db\Migrations\Migration;

class AddMissingThrottleRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();

        $throttleTable = \Config::get('packages.netinteractive.sentry.config.throttle_table');

        try {
            Schema::table($throttleTable, function($table)
            {
                $userTable = \Config::get('packages.netinteractive.sentry.config.user_table');

                $table->foreign('user__id')->references('id')->on($userTable)->onDelete('cascade');
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
