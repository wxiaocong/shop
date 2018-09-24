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
	protected $fillable = ['mobile', 'openid', 'headimgurl', 'nickname', 'subscribe', 'subscribe_time', 'city', 'province', 'country', 'sex'];
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

	/**
	 * Get the relational models of Areas.
	 *
	 * @return App\Models\Areas
	 */
	public function provinceObj() {
		return $this->belongsTo('App\Models\Areas', 'province', 'id');
	}

	/**
	 * Get the relational models of Areas.
	 *
	 * @return App\Models\Areas
	 */
	public function cityObj() {
		return $this->belongsTo('App\Models\Areas', 'city', 'id');
	}

	/**
	 * Get the relational models of Areas.
	 *
	 * @return App\Models\Areas
	 */
	public function areaObj() {
		return $this->belongsTo('App\Models\Areas', 'area', 'id');
	}

	/**
	 * Get the relational models of Areas.
	 *
	 * @return App\Models\Areas
	 */
	public function companyProvinceObj() {
		return $this->belongsTo('App\Models\Areas', 'company_province', 'id');
	}
}
