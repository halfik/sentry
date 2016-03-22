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
        if (Schema::hasTable('role')){
            DB::table('role')->insert(
                array(
                    'code' => 'admin',
                    'name' => _('Administrator'),
                    'is_hidden' => false,
                    'weight' => 0
                )
            );

            DB::table('role')->insert(
                array(
                    'code' => 'user',
                    'name' => _('Użytkownik'),
                    'is_hidden' => true,
                    'weight' => 80
                )
            );

            DB::table('role')->insert(
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
        if (Schema::hasTable('role')){
            DB::table('role')->where('name', 'admin')->delete();
            DB::table('role')->where('name', 'user')->delete();
            DB::table('role')->where('name', 'guest')->delete();

            Schema::table('role', function(Blueprint $table)
            {
                $table->dropColumn('is_hidden');
            });
        }

    }

}
