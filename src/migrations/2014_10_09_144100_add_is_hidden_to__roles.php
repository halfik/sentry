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


        $userRole = App::make('sentry')->getGroupProvider()->findByCode('user');
        $guestRole = App::make('sentry')->getGroupProvider()->findByCode('guest');

        $userRole->is_hidden = true;
        $userRole->save();

        $guestRole->is_hidden = true;
        $guestRole->save();

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }

}
