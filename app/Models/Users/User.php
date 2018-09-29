<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *--------------------------------------------------------------------------
 * User Model
 *--------------------------------------------------------------------------
 *
 * This model is responsible for tables of 'users'.
 *
 */
class User extends Model {

	use SoftDeletes;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 *  The attributes that are mass assignable
	 * @var array
	 */
	protected $fillable = ['referee_id', 'mobile', 'openid', 'headimgurl', 'nickname', 'subscribe', 'subscribe_time', 'city', 'province', 'country', 'balance','sex','level','vip'];
	/**
	 * The column used by SoftDeletes.
	 *
	 * @var array.
	 */
	protected $dates = array('deleted_at');

	/**
	 * Get the relational models of ExpressAddress.
	 *
	 * @return array[App\Models\Users\ExpressAddress]
	 */
	public function expressAddress() {
		return $this->hasMany('App\Models\Users\ExpressAddress');
	}
}
