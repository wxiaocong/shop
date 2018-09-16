<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * SystemLog Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'admin_system_logs'.
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
    protected $table = 'admin_system_logs';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational model of User.
     *
     * @return App\Models\Admins\User
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Admins\AdminUser', 'user_id', 'id')->withTrashed();
    }

    /**
     * Get the relational models of SystemLogDetail.
     *
     * @return array[App\Models\Admins\SystemLogDetail]
     */
    public function systemLogDetails()
    {
        return $this->hasMany('App\Models\Admins\SystemLogDetail', 'system_log_id', 'id');
    }
}
