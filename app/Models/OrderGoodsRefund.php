<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * order_goods_refund Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'order_goods_refund'.
 *
 */
class OrderGoodsRefund extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'order_goods_refund';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational model of order.
     *
     * @return array(App\Models\OrderRefund)
     */
    public function orderRefund()
    {
        return $this->belongsTo('App\Models\OrderRefund', 'refund_id');
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
