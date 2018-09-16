<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * AdminRightCategory Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'admin_rights_categories'.
 *
 */
class AdminRightCategory extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'admin_rights_categories';

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
     * Get the relational models of AdminRightCategory.
     *
     * @return App\Models\Admins\AdminRightCategory
     */
    public function parentCategory()
    {
        return $this->belongsTo('App\Models\Admins\AdminRightCategory', 'parent_id', 'id');
    }

    /**
     * Get the relational models of AdminRightCategory.
     *
     * @return array[App\Models\Admins\AdminRightCategory]
     */
    public function childCategries()
    {
        return $this->hasMany('App\Models\Admins\AdminRightCategory', 'parent_id', 'id');
    }

    /**
     * Get the relational models of AdminRight.
     *
     * @return array[App\Models\Admins\AdminRight]
     */
    public function rights()
    {
        return $this->hasMany('App\Models\Admins\AdminRight', 'category_id', 'id');
    }
}
