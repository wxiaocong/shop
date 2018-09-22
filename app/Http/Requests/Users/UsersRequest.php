<?php namespace App\Http\Requests\Users;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * UsersRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update users
 *
 * @author wangcong@carnetmotor.com
 *
 */
class UsersRequest extends Request {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return array(
			'nickname' => 'max:100',
			'realname' => 'max:100',
			'mobile' => 'max:20',
			'email' => 'email',
			'birthday' => 'date',
		);
	}

	/**
	 *  add validation messages
	 *
	 * @return [array] [validation message]
	 */
	public function messages() {
		return array(
			'nickname.max' => '昵称最多100个字符',
			'email.email' => '邮箱格式错误',
			'birthday.date' => '日期格式错误',
			'realname.max' => '姓名最多100字符',
			'mobile.max' => '手机号格式错误',
		);
	}

	/**
	 *  format errors
	 * @param  Validator $validator
	 * @return [type]
	 */
	protected function formatErrors(Validator $validator) {
		return $validator->errors()->all();
	}
}
