<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * Category Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'category'.
 *
 */
class Category extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'category';

    /**
     *  The attributes that are mass assignable
     * @var array
     */
    protected $fillable = array('name', 'parent_id', 'pic', 'sort', 'state');
    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational models of Goods.
     *
     * @return array[App\Models\Goods]
     */
    public function goods()
    {
        return $this->hasMany('App\Models\Goods');
    }

    /**
     * Get the relational models of brand.
     *
     * @return array[App\Models\Brand]
     */
    public function brand()
    {
        return $this->belongsToMany('App\Models\Brand', 'goods', 'category_parent_id', 'brand_id');
    }

    /**
     * Get the relational models of AdminRightCategory.
     *
     * @return App\Models\Admins\AdminRightCategory
     */
    public function parentCategory()
    {
        return $this->belongsTo('App\Models\Category', 'parent_id', 'id');
    }
}
