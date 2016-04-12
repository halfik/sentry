<?php
use Illuminate\Database\Migrations\Migration;

class MigrationNiDefaultRoles extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName =  \Config::get('packages.netinteractive.sentry.config.role_table');

        if (Schema::hasTable($tableName)){
            DB::table($tableName)->insert(
                array(
                    'code' => 'admin',
                    'name' => _('Administrator'),
                    'is_hidden' => false,
                    'weight' => 0
                )
            );

            DB::table($tableName)->insert(
                array(
                    'code' => 'user',
                    'name' => _('Użytkownik'),
                    'is_hidden' => true,
                    'weight' => 80
                )
            );

            DB::table($tableName)->insert(
                array(
                    'code' => 'guest',
                    'name' => _('Gość'),
                    'is_hidden' => true,
                    'weight' => 100
                )
            );


        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableName =  \Config::get('packages.netinteractive.sentry.config.role_table');

        if (Schema::hasTable($tableName)){
            DB::table($tableName)->where('name', 'admin')->delete();
            DB::table($tableName)->where('name', 'user')->delete();
            DB::table($tableName)->where('name', 'guest')->delete();
        }

    }

}
