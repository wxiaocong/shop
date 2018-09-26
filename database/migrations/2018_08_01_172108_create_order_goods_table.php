<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create order_goods table
 *--------------------------------------------------------------------------
 *
 *
 */
class CreateOrderGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('order_goods', function (Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();

			$table->integer('order_id')->unsigned()->comment('订单id');
			$table->integer('goods_id')->unsigned()->comment('商品id');
			$table->integer('spec_id')->unsigned()->comment('商品spec_id');
			$table->string('goods_name', 50)->comment('商品名称');
			$table->string('spec_values', 255)->comment('商品spec属性');
			$table->string('goods_img', 200)->comment('商品图片');
			$table->integer('num')->unsigned()->default(1)->comment('数量');
			$table->integer('send_num')->unsigned()->default(0)->comment('已发货数量');
			$table->integer('return_num')->unsigned()->default(0)->comment('已退货数量');
			$table->bigInteger('prime_cost')->unsigned()->comment('原单价');
			$table->bigInteger('price')->unsigned()->comment('下单单价');
			$table->bigInteger('total_price')->unsigned()->comment('下单总价');
			$table->tinyInteger('state')->default(1)->comment('状态1未发货2部分发货3全部发货4部分退货5全部退货6取消');

			// create index
			$table->index('order_id');
			$table->index('goods_id');
			$table->index('spec_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('order_goods');
	}
}
