<?php namespace App\Http\Requests\Admins\Goods;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * CategoryRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update category
 *
 * @author wangcong@carnetmotor.com
 *
 */
class CategoryRequest extends Request
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
            'name'      => 'required|max:60',
            'parent_id' => 'integer',
            'sort'      => 'integer',
            'state'     => 'required|in:1,2'
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
            'name.required'     => '请输入分类名称',
            'name.max'          => '分类名称最多60个字符',
            'parent_id.integer' => '上级分类id错误',
            'sort.integer'      => '排序错误',
            'state.required'    => '请选择首页是否显示',
            'state.in'          => '首页是否显示错误',
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
