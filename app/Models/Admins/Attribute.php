<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * Attribute Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'attributes'.
 *
 */
class Attribute extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'attributes';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational model of Model.
     *
     * @return App\Models\Admins\Model
     */
    public function model()
    {
        return $this->belongsTo('App\Models\Admins\Model', 'model_id', 'id');
    }
    
    /**
     * Get the relational model of goods_attr.
     *
     * @return App\Models\GoodsAttr
     */
    public function goodsAttr()
    {
        return $this->hasMany('App\Models\GoodsAttr', 'attr_ids');
    }
}
