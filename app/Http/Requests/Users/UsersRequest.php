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
class UsersRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array(
            'password'  =>  'min:6|max:20',
            'nickname'  =>  'max:100',
            'email'     =>  'email',
            'birthday'  =>  'date',
            'province'  =>  'integer',
            'city'      =>  'integer',
            'area'      =>  'integer',
            'address'   =>  'max:255'
        );
    }

    /**
     *  add validation messages
     *
     * @return [array] [validation message]
     */
    public function messages()
    {
        return array(
            'password.min'  => '密码至少6位',
            'password.max'  => '密码最多20位',
            'nickname.max'  => '昵称最多100个字符',
            'email.email'   => '邮箱格式错误',
            'birthday.date' => '日期格式错误',
            'province.integer'=> '省格式错误',
            'city.integer'  => '市格式错误',
            'area.integer'  => '区格式错误',
            'address.max'   => '详情地址最多255字符'
        );
    }

    /**
     *  format errors
     * @param  Validator $validator
     * @return [type]
     */
    protected function formatErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }
}
