<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * update Users table
 *--------------------------------------------------------------------------
 *
 *
 */
class UpdateUsersTable20181007172130 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('lockBalance')->unsigned()->default(0)->after('balance')->comment('锁定金额');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('lockBalance');
        });
    }
}
