<?php
/**
 * Part of the Sentry package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentryInstallGroups extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName =  \Config::get('netinteractive.sentry.role_table');

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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableName =  \Config::get('netinteractive.sentry.role_table');

        Schema::drop($tableName);
    }

}
