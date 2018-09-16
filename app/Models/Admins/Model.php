<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * Model Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'models'.
 *
 */
class Model extends \Illuminate\Database\Eloquent\Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'models';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational models of Attribute.
     *
     * @return array[App\Models\Admins\Attribute]
     */
    public function attributes()
    {
        return $this->hasMany('App\Models\Admins\Attribute', 'model_id', 'id');
    }
}
