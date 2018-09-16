<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * AdminRole Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'admin_roles'.
 *
 */
class AdminRole extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'admin_roles';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational model of AdminUser.
     *
     * @return App\Models\Admins\AdminUser
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Admins\AdminUser', 'user_id', 'id')->withTrashed();
    }

    /**
     * Get the relational model of AdminUser.
     *
     * @return array(App\Models\Admins\AdminUser)
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\Admins\AdminUser', 'admin_role_users', 'role_id', 'user_id');
    }

    /**
     * Get the relational model of AdminRight.
     *
     * @return array(App\Models\Admins\AdminRight)
     */
    public function rights()
    {
        return $this->belongsToMany('App\Models\Admins\AdminRight', 'admin_role_rights', 'role_id', 'rights_id');
    }
}
