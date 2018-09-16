<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * AdminUser Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'admin_users'.
 *
 */
class AdminUser extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'admin_users';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational model of AdminRole.
     *
     * @return array(App\Models\Admins\AdminRole)
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Admins\AdminRole', 'admin_role_users', 'user_id', 'role_id');
    }
}
