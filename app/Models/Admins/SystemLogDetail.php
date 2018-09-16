<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * SystemLogDetail Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'admin_system_log_details'.
 *
 */
class SystemLogDetail extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'admin_system_log_details';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational model of SystemLog.
     *
     * @return App\Models\PmsSystemLog
     */
    public function systemLog()
    {
        return $this->belongsTo('App\Models\Admins\SystemLog', 'system_log_id', 'id')->withTrashed();
    }
}
