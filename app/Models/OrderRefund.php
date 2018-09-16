<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * order_refund Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'order_refund'.
 *
 */
class OrderRefund extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'order_refund';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational models of OrderGoods.
     *
     * @return array[App\Models\OrderGoods]
     */
    public function orderGoodsRefunds()
    {
        return $this->hasMany('App\Models\OrderGoodsRefund', 'refund_id');
    }
}
