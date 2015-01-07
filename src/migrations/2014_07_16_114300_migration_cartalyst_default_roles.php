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

            Schema::table('roles', function(\Illuminate\Database\Schema\Blueprint $table)
            {
                $table->boolean('is_hidden')->default(false);
            });

            DB::table('roles')->insert(
                array(
                    'name' => 'admin'
                )
            );

            DB::table('roles')->insert(
                array(
                    'name' => 'user'
                )
            );

            DB::table('roles')->insert(
                array(
                    'name' => 'guest',
                    'is_hidden' => 1
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
