<?php
use Illuminate\Database\Migrations\Migration;

class MigrationCartalystRenameUsersGroups extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('users_groups')){
            Schema::rename('users_groups', 'users_roles');

        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('users_roles')){
            Schema::rename('users_roles', 'users_groups');

        }

    }

}
