<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * GoodsSpec Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'goods_spec'.
 *
 */
class GoodsSpec extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'goods_spec';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational model of Brand.
     *
     * @return array(App\Models\Brand)
     */
    public function brand() {
        return $this->belongsTo('App\Models\Brand');
    }
    /**
     * Get the relational model of Category.
     *
     * @return array(App\Models\Category)
     */
    public function category() {
        return $this->belongsTo('App\Models\Category');
    }
    /**
     * Get the relational model of OrderGoods.
     *
     * @return array(App\Models\OrderGoods)
     */
    public function orderGoods()
    {
        return $this->hasMany('App\Models\OrderGoods','spec_id');
    }
    
    /**
     * Get the relational model of Goods.
     *
     * @return array(App\Models\Goods)
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods');
    }

}
