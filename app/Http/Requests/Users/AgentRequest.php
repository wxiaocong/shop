<?php namespace App\Http\Requests\Users;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * AgentRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update agent
 *
 * @author wangcong@carnetmotor.com
 *
 */
class AgentRequest extends Request
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
            'level'       => 'required|integer',
            'agent_name'  => 'required|max:100',
            'mobile'      => 'required|digits:11',
            'idCard' => 'required',
            'province'    => 'required|integer',
            'city'        => 'required|integer',
            'area'        => 'required|integer',
            'address'     => 'required|max:200',
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
            'level.required'  => '请选择合作类型',
            'level.integer'=> '合作类型格式错误',
            'agent_name.required'  => '请填写代理商姓名',
            'agent_name.max'     => '代理商姓名最多100字符',
            'mobile.required' => '手机号必填',
            'mobile.digits'   => '手机号格式错误',
            'idCard.required' => '身份证必填',
            'province.required'  => '请填写代理商地区',
            'province.integer'=> '省格式错误',
            'city.required'  => '请填写代理商地区',
            'city.integer'  => '市格式错误',
            'area.integer'  => '区格式错误',
            'address.required'=> '详情地址必填',
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
