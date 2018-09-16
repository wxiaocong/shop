<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * User Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'wechat_users'.
 *
 */
class WechatUser extends Model
{

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'wechat_users';

    /**
     *  The attributes that are mass assignable
     * @var array
     */
    protected $fillable = ['user_id','nickname','openid','subscribe','subscribe_time','headimgurl','city','country','province','sex','bind_time','unbind_time','last_ip','last_time'];

    /**
     * The column used by SoftDeletes.
     *
     * @var array.
     */
    protected $dates = array('deleted_at');

    /**
     * Get the relational models of User.
     *
     * @return App\Models\Users\User
     */
    public function user()
    {
        return $this->hasOne('App\Models\Users\User', 'id', 'user_id');
    }
}
