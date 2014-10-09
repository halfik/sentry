<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddIsHiddenToRoles extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function(Blueprint $table)
        {
            $table->boolean('is_hidden')->default(false);
        });

        $userRole = App::make('sentry')->getGroupProvider()->findByCode('user');
        $guestRole = App::make('sentry')->getGroupProvider()->findByCode('guest');
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function(Blueprint $table)
        {
            $table->dropColumn('is_hidden');
        });
    }

}
