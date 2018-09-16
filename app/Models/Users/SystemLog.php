<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * SystemLog Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'user_system_logs'.
 *
 */
class SystemLog extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_system_logs';

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
     * Get the relational models of SystemLogDetail.
     *
     * @return array[App\Models\Users\SystemLogDetail]
     */
    public function systemLogDetails()
    {
        return $this->hasMany('App\Models\Users\SystemLogDetail', 'system_log_id', 'id');
    }
}
