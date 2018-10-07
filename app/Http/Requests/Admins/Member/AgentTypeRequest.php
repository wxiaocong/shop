<?php namespace App\Http\Requests\Admins\Member;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * AgentTypeRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update agent_type
 *
 *
 */
class AgentTypeRequest extends Request
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
            'type_name'     => 'required|max:100',
            'price'         => 'required|numeric',
            'goodsNum'      => 'required|integer',
            'returnMoney'   =>  'numeric'
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
            'name.required'   => '合伙类型名称不能为空',
            'name.max'        => '合伙类型名称最多100个字符',
            'price.required'   => '价格不能为空',
            'price.numeric'    => '价格类型错误',
            'goodsNum.required'   => '配货数量不能为空',
            'goodsNum.integer'    => '配货数量类型错误',
            'returnMoney.numeric'    => '返利金额类型错误',
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
