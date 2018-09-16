<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * AdminRight Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'admin_rights'.
 *
 */
class AdminRight extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'admin_rights';

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
     * Get the relational model of AdminRightCategory.
     *
     * @return App\Models\Admins\AdminRightCategory
     */
    public function rightCategory()
    {
        return $this->belongsTo('App\Models\Admins\AdminRightCategory', 'category_id', 'id');
    }

    /**
     * Get the relational model of AdminRole.
     *
     * @return array(App\Models\Admins\AdminRole)
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Admins\AdminRole', 'admin_role_rights', 'rights_id', 'role_id');
    }
}
