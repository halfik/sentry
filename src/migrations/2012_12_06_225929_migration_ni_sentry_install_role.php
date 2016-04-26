<?php
use Illuminate\Database\Migrations\Migration;

class MigrationNiSentryInstallRole extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName =  \Config::get('packages.netinteractive.sentry.config.role_table');

        if (!Schema::hasTable($tableName)){
            Schema::create($tableName, function($table)
            {
                $table->increments('id');
                $table->string('name');
                $table->string('code');
                $table->text('permissions')->nullable();
                $table->boolean('is_hidden')->default(false);
                $table->smallInteger('weight', false, true)->default(0);

                $table->timestamp('created_at')->default(DB::raw('now()'));
                $table->timestamp('updated_at')->nullable();

                // We'll need to ensure that MySQL uses the InnoDB engine to
                // support the indexes, other engines aren't affected.
                $table->engine = 'InnoDB';
                $table->unique('name');
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
        $tableName =  \Config::get('packages.netinteractive.sentry.config.role_table');

        Schema::drop($tableName);
    }

}
