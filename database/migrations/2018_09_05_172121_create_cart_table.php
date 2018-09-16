<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 *--------------------------------------------------------------------------
 * create cart table
 *--------------------------------------------------------------------------
 *
 * @author wangcong(wangcong@carnetmotors.com)
 *
 */
class CreateCartTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cart', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->integer('spec_id')->default(0)->comment('skuid');
            $table->integer('num')->default(1)->comment('商品数量');

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
        Schema::dropIfExists('cart');
    }
}
