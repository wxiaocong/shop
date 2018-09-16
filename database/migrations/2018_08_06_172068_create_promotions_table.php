<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create promotions table
 *--------------------------------------------------------------------------
 *
 * @author caopei(caopei@carnetmotors.com)
 *
 */
class CreatePromotionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->date('start_time')->comment('开始时间');
            $table->date('end_time')->comment('结束时间');
            $table->smallInteger('sort')->default(0)->comment('排序');
            $table->text('condition')->comment('活动生效条件 当type=0<促销规则消费额度>,当type=1<限时抢购商品ID>,type=2<特价商品分类ID>,type=3<特价商品ID>,type=4<特价商品品牌ID>,type=5<无意义>');
            $table->tinyInteger('type')->default(0)->comment('活动类型 0:购物车促销规则 1:商品限时抢购 2:商品分类特价 3:商品单品特价 4:商品品牌特价 5:新用户注册促销规则');
            $table->text('award_value')->nullable()->comment('奖励值 type=0,5<奖励值>,type=1<抢购价格>,type=2,3,4<特价折扣>');
            $table->string('name', 100)->comment('活动名称');
            $table->text('intro')->nullable()->comment('活动介绍');
            $table->tinyInteger('award_type')->default(0)->comment('奖励方式:0商品限时抢购 1减金额 2奖励折扣 3赠送积分 4赠送代金券 5赠送赠品 6免运费 7商品特价 8赠送经验');
            $table->tinyInteger('is_close')->default(0)->comment('是否关闭 0:否 1:是');
            $table->text('user_group')->nullable()->comment('允许参与活动的用户组,all表示所有用户组');
            $table->integer('seller_id')->unsigned()->default(0)->comment('商家ID');
            $table->integer('order_num')->default(0)->comment('下单数量');
            $table->integer('selled_num')->default(0)->comment('已售数量');

            // create index
            $table->index('deleted_at');
            $table->index('seller_id');
            $table->index('type');
            $table->index('start_time');
            $table->index('end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotions');
    }
}
