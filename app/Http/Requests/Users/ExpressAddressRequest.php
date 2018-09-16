<?php namespace App\Http\Requests\Users;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * ExpressAddressRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update ExpressAddress
 *
 * @author wangcong@carnetmotor.com
 *
 */
class ExpressAddressRequest extends Request
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
            'to_user_name'=> 'required|max:60',
            'mobile'      => 'required|digits:11',
            'province'    => 'required|integer',
            'city'        => 'integer',
            'area'        => 'integer',
            'address'     => 'max:200',
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
            'to_user_name.required'=> '收货人必填',
            'to_user_name.max'=> '收货人最多60个字符',
            'mobile.required' => '手机号必填',
            'mobile.digits'   => '手机号格式错误',
            'province.integer'=> '省格式错误',
            'city.integer'  => '市格式错误',
            'area.integer'  => '区格式错误',
            'address.required'=> '详情地址必填',
            'birthday.date' => '日期格式错误',
            'address.max'     => '详情地址最多200字符'
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
