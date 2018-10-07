<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * withdraw Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'withdraw'.
 *
 */
class Withdraw extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the withdraw.
     *
     * @var string
     */
    protected $table = 'withdraw';
    
    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

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
