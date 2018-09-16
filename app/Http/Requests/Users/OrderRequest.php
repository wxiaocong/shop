<?php namespace App\Http\Requests\Users;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * OrderRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update users
 *
 * @author wangcong@carnetmotor.com
 *
 */
class OrderRequest extends Request
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
            'num'           =>  'required|integer|min:1',
            'activity_id'   =>  'integer',
            'spec_id'       =>  'integer',
            'express_id'    =>  'required|integer'
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
            'num.required'  => '请选择商品数量',
            'num.integer'  => '商品数量格式错误',
            'num.min'  => '商品数量错误',
            'activity_id.integer'  => '活动id格式错误',
            'spec_id.integer'   => '规格错误',
            'express_id.required' => '请填写收货地址',
            'express_id.integer'=> '收货地址格式错误'
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
