<?php namespace App\Http\Requests\Admins\System;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * AreasRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update areas
 *
 * @author wangcong@carnetmotor.com
 *
 */
class AreasRequest extends Request
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
            'parent_id' =>  'integer',
            'area_name' => 'max:50',
            'sort'      => 'integer'
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
            'parent_id.integer'  => '上级id格式错误',
            'area_name.max'      => '地区名称最多50个字符',
            'sort.integer'       => '排序错误'
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
