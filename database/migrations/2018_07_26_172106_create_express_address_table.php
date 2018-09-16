<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create express_address table
 *--------------------------------------------------------------------------
 *
 * @author wangcong(wangcong@carnetmotors.com)
 *
 */
class CreateExpressAddressTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('express_address', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            
            $table->integer('user_id')->unsigned()->comment('用户id');
            $table->string('to_user_name', 60)->comment('收货人姓名');
            $table->string('mobile', 20)->comment('手机号码');
            $table->integer('province')->unsigned()->comment('省');
            $table->integer('city')->unsigned()->comment('市');
            $table->integer('area')->unsigned()->comment('区');
            $table->string('address', 200)->comment('详情地址');
            $table->string('zipcode', 10)->nullable()->comment('邮编');
            $table->tinyInteger('is_default')->default(0)->comment('默认地址1是0否');
            
            // create index
            $table->index('user_id');
            $table->index('mobile');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('express_address');
    }
}
