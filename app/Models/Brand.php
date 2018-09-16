<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * Brand Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'brand'.
 *
 */
class Brand extends Model
{
    
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'brand';
    
    /**
     *  The attributes that are mass assignable
     * @var array
     */
    protected $fillable = ['logo_cname', 'logo_ename', 'short_name', 'mini_desc', 'short_desc', 'detail_desc', 'state', 'sort'];
    
    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');
    
    /**
     * Get the relational models of Goods.
     *
     * @return array[App\Models\Admins\Goods]
     */
    public function goods()
    {
        return $this->hasMany('App\Models\Admins\Goods');
    }
}
