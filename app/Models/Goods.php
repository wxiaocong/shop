<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * Goods Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'goods'.
 *
 */
class Goods extends Model {

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'goods';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational model of Category.
     *
     * @return array(App\Models\Category)
     */
    public function category() {
        return $this->belongsTo('App\Models\Category');
    }

    /**
     * Get the relational models of GoodsSpec.
     *
     * @return array[App\Models\GoodsSpec]
     */
    public function goodsSpecs() {
        return $this->hasMany('App\Models\GoodsSpec', 'goods_id', 'id');
    }

    /**
     * Get the relational model of GoodsAttr.
     *
     * @return array(App\Models\GoodsAttr)
     */
    public function goodsAttrs() {
        return $this->hasMany('App\Models\GoodsAttr', 'goods_id', 'id');
    }

    /**
     * Get the relational model of OrderGoods.
     *
     * @return array(App\Models\OrderGoods)
     */
    public function orderGoods() {
        return $this->hasMany('App\Models\OrderGoods');
    }
    
    /**
     * Get the relational model of GoodsSpec.
     *
     * @return array(App\Models\GoodsSpec)
     */
    public function goodsSpec() {
        return $this->hasMany('App\Models\GoodsSpec');
    }
}
