<?php
use Illuminate\Database\Migrations\Migration;

class MigrationCartalystRenameGroups extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('groups')){
            Schema::rename('groups', 'roles');

            Schema::table('roles', function($table)
            {
                $table->dropColumn('created_at');
                $table->dropColumn('updated_at');
            });

            Schema::table('roles', function($table)
            {
                $table->timestamp('created_at')->default(DB::raw('now()'));
                $table->timestamp('updated_at')->nullable();
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
        if (Schema::hasTable('roles')){
            Schema::rename('roles', 'groups');
            //DB::statement("ALTER TABLE `groups` ALTER COLUMN `created_at` DROP DEFAULT;");
           // DB::statement("ALTER TABLE `groups` ALTER COLUMN `updated_at` DROP DEFAULT;");
        }
    }

}
