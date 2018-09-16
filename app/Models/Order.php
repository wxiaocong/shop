<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * order Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'order'.
 *
 */
class Order extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'order';
    
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
    public function orderGoods()
    {
        return $this->hasMany('App\Models\OrderGoods');
    }

    /**
     * Get the relational model of User.
     *
     * @return App\Models\Users\User
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Users\User', 'user_id', 'id')->withTrashed();
    }
}
