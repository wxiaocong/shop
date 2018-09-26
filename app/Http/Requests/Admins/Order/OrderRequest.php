<?php namespace App\Http\Requests\Admins\Order;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * OrderRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate deliver model
 *
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
            'expressName' => 'required|max:50',
            'expressNo'   => 'required',
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
            'expressName.required' => '请输入快递物流公司名称',
            'expressName.max'      => '快递物流公司名称最多50个字符',
            'expressNo.required'   => '请输入快递单号不能为空',
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
