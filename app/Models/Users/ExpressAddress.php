<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * User Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'express_address'.
 *
 */
class ExpressAddress extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'express_address';
    
    /**
     *  The attributes that are mass assignable
     * @var array
     */
    protected $fillable = ['user_id', 'to_user_name', 'mobile', 'province', 'city', 'area', 'address', 'is_default'];
    
    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');
    
    /**
     * Get the relational model of User.
     *
     * @return array(App\Models\Users\User)
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Users\User');
    }
}
