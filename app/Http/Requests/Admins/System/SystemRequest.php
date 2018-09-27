<?php namespace App\Http\Requests\Admins\System;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * SystemRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update system
 *
 *
 */
class SystemRequest extends Request
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
            'name'     => 'required|max:60',
            'val'      => 'required|max:60',
            'desc'     => 'max:200'
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
            'name.required'   => '参数名不能为空',
            'name.max'        => '参数名最多60个字符',
            'val.required'   => '参数值不能为空',
            'val.max'        => '参数值最多60个字符',
            'desc.max'       => '描述值最多200个字符'
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
