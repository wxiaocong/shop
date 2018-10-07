<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create agent_type table
 *--------------------------------------------------------------------------
 *
 */
class CreateAgentTypeTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('agent_type', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('type_name', 100)->comment('合伙类型名称');
            $table->integer('price')->comment('价格');
            $table->integer('returnMoney')->comment('返利');
            $table->integer('goodsNum')->default(0)->comment('配货数量');
            $table->tinyInteger('state')->default(1)->comment('0禁用1启用');
            $table->string('remark', 200)->nullable()->comment('备注');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('agent_type');
    }
}
