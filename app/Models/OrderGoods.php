<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * order_goods Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'order_goods'.
 *
 */
class OrderGoods extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'order_goods';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational model of order.
     *
     * @return array(App\Models\Order)
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

    /**
     * Get the relational model of goods.
     *
     * @return array(App\Models\goods)
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods', 'goods_id');
    }

    /**
     * Get the relational model of goods_spec.
     *
     * @return array(App\Models\goods_spec)
     */
    public function goodsSpec()
    {
        return $this->belongsTo('App\Models\GoodsSpec', 'spec_id');
    }
}
