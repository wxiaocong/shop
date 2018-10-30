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
class UpdateUsersTable20181030172141 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('opening_bank', 64)->default('')->after('enc_bank_no')->comment('开户行信息');
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
            $table->dropColumn('opening_bank');
        });
    }
}
