<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * Agent Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'agent'.
 *
 */
class Agent extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'agent';
    
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

     /**
     * Get the relational models of Areas.
     *
     * @return App\Models\Areas
     */
    public function provinceObj()
    {
        return $this->belongsTo('App\Models\Areas', 'province', 'id');
    }

    /**
     * Get the relational models of Areas.
     *
     * @return App\Models\Areas
     */
    public function cityObj()
    {
        return $this->belongsTo('App\Models\Areas', 'city', 'id');
    }

    /**
     * Get the relational models of Areas.
     *
     * @return App\Models\Areas
     */
    public function areaObj()
    {
        return $this->belongsTo('App\Models\Areas', 'area', 'id');
    }
}
