<?php namespace App\Http\Requests\Admins\Goods;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * ModelRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update model
 *
 * @author caopei@carnetmotor.com
 *
 */
class ModelRequest extends Request
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
            'name'                 => 'required|max:60',
            'attribute.showType.*' => 'required|in:1,2,3,4',
            'attribute.name.*'     => 'required|max:60',
            'attribute.isSearch.*' => 'required|in:0,1',
            'attribute.isSpec.*'   => 'required|in:0,1',
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
            'name.required'                 => '请输入模型名称',
            'name.max'                      => '模型名称最多60个字符',
            'attribute.showType.*.required' => '请选择操作类型',
            'attribute.showType.*.in'       => '操作类型错误',
            'attribute.name.*.required'     => '请输入属性名',
            'attribute.name.*.max'          => '属性名最多60个字符',
            'attribute.isSearch.*.required' => '请勾选商品筛选项',
            'attribute.isSearch.*.in'       => '商品筛选项错误',
            'attribute.isSpec.*.required'   => '请勾选规格选项',
            'attribute.isSpec.*.in'         => '规格选项错误',
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
