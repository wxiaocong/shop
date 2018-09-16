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
class UpdateUsersTable20180904172116 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_business')->default(0)->after('email')->comment('0普通用户1商家');
            $table->string('company_name', 200)->nullable()->after('is_business')->comment('公司名称');
            $table->string('company_address', 200)->nullable()->after('company_name')->comment('公司地址');
            $table->integer('company_province')->unsigned()->nullable()->after('company_address')->comment('公司-省');
            $table->integer('company_city')->unsigned()->nullable()->after('company_province')->comment('公司-市');
            $table->integer('company_area')->unsigned()->nullable()->after('company_city')->comment('公司-区');
            $table->integer('shop_site')->nullable()->after('company_area')->comment('店铺工位');
            $table->string('business_license',200)->nullable()->after('shop_site')->comment('营业执照');
            $table->string('doorhead_photo',200)->nullable()->after('business_license')->comment('门头照片');
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
            $table->dropColumn('is_business');
            $table->dropColumn('company_name');
            $table->dropColumn('company_address');
            $table->dropColumn('company_province');
            $table->dropColumn('company_city');
            $table->dropColumn('company_area');
            $table->dropColumn('shop_site');
            $table->dropColumn('business_license');
            $table->dropColumn('doorhead_photo');
        });
    }
}
