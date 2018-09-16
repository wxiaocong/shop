<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * Areas Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'goods'.
 *
 */
class Goods extends Model
{
    
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'goods';
    
    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');
    
    /**
     * Get the relational model of Category.
     *
     * @return array(App\Models\Admins\Category)
     */
    public function belongsToCategory()
    {
        return $this->belongsTo('App\Models\Admins\Category', 'category_id', 'id');
    }
    
    /**
     * Get the relational model of Brand.
     *
     * @return array(App\Models\Admins\Brand)
     */
    public function belongsToBrand()
    {
        return $this->belongsTo('App\Models\Admins\Brand', 'brand_id', 'id');
    }
}
