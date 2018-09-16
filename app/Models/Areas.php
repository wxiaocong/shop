<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * Areas Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'areas'.
 *
 */
class Areas extends Model
{
    
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'areas';
    
    /**
     *  The attributes that are mass assignable
     * @var array
     */
    protected $fillable = ['parent_id', 'area_name', 'sort'];
    
    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');
}
