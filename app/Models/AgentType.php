<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * AgentType Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'agent_type'.
 *
 */
class AgentType extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'agent_type';

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');
}
