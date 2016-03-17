<?php
use Illuminate\Database\Migrations\Migration;

class MigrationCartalystDefaultRoles extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        if (Schema::hasTable('roles')){
            DB::table('roles')->insert(
                array(
                    'code' => 'admin',
                    'name' => 'Administrator',
                    'is_hidden' => false,
                    'weight' => 0
                )
            );

            DB::table('roles')->insert(
                array(
                    'code' => 'user',
                    'name' => 'Użytkownik',
                    'is_hidden' => true,
                    'weight' => 80
                )
            );

            DB::table('roles')->insert(
                array(
                    'code' => 'guest',
                    'name' => 'Gość',
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
        if (Schema::hasTable('roles')){
            DB::table('roles')->where('name', 'admin')->delete();
            DB::table('roles')->where('name', 'user')->delete();
            DB::table('roles')->where('name', 'guest')->delete();

            Schema::table('roles', function(Blueprint $table)
            {
                $table->dropColumn('is_hidden');
            });
        }

    }

}
