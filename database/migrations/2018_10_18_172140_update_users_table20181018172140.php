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
class UpdateUsersTable20181018172140 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('bank_code', 64)->default('')->after('realname')->comment('开户行');
            $table->string('enc_bank_no', 64)->default('')->after('bank_code')->comment('银行卡号');
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
            $table->dropColumn('bank_code');
            $table->dropColumn('enc_bank_no');
        });
    }
}
