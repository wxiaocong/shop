<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * update users table
 *--------------------------------------------------------------------------
 *
 * @author wangcong(wangcong@carnetmotor.com)
 *
 */
class UpdateUsersTable20180905172120 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('business_apply_time')->nullable()->comment('申请商家时间');
            $table->dateTime('business_audit_time')->nullable()->comment('审核商家时间');
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
            $table->dropColumn('business_apply_time');
            $table->dropColumn('business_audit_time');
        });
    }
}
