<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * PayLogs Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'pay_logs'.
 *
 */
class PayLogs extends Model
{
    
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pay_logs';
    
    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational models of users.
     *
     * @return array[App\Models\Users\User]
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Users\User','user_id');
    }
}
