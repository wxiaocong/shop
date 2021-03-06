<?php

namespace App\Models\Merchants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * SystemLogDetail Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'merchant_system_log_details'.
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
    protected $table = 'merchant_system_log_details';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational model of SystemLog.
     *
     * @return App\Models\Merchants\SystemLog
     */
    public function systemLog()
    {
        return $this->belongsTo('App\Models\Merchants\SystemLog', 'system_log_id', 'id')->withTrashed();
    }
}
