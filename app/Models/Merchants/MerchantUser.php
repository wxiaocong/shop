<?php

namespace App\Models\Merchants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * MerchantUser Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'merchant_users'.
 *
 */
class MerchantUser extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'merchant_users';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');
}
