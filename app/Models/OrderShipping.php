<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * OrderShipping Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'order_shipping'.
 *
 */
class OrderShipping extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'order_shipping';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational model of Model.
     *
     * @return App\Models\OrderGoods
     */
    public function orderGood()
    {
        return $this->belongsTo('App\Models\OrderGoods', 'order_goods_id', 'id');
    }
}
