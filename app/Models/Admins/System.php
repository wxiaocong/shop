<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * System Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'system'.
 *
 */
class System extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'system';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');
}
