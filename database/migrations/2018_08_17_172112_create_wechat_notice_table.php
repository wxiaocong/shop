<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create wechat_notice table
 *--------------------------------------------------------------------------
 *
 *
 */
class CreateWechatNoticeTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_notice', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('user_id')->default(0)->comment('用户id');
            $table->integer('order_id')->default(0)->comment('订单id');
            $table->string('open_id', 50)->comment('微信openid');
            $table->string('template_id', 50)->comment('微信通知模板id');
            $table->tinyInteger('is_send')->default(0)->comment('0未发1已发');
            
            // create index
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wechat_notice');
    }
}
