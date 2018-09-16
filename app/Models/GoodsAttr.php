<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * GoodsAttr Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'goods_attr'.
 *
 */
class GoodsAttr extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'goods_attr';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');
    
    
    /**
     * Get the relational model of attribute.
     *
     * @return array(App\Models\Admins\Attribute)
     */
    public function attribute() {
        return $this->belongsTo('App\Models\Admins\Attribute','attr_ids');
    }
}
