<?php namespace App\Http\Requests\Admins\Goods;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * GoodRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update model
 *
 * @author caopei@carnetmotor.com
 *
 */
class GoodRequest extends Request
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
            'name'           => 'required|max:50',
            'goodCategoryId' => 'required|exists:category,id',
            'brandId'        => 'required|exists:brand,id',
            'virtualType'    => 'required|in:0,1',
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
            'name.required'           => '请输入商品名称',
            'name.max'                => '商品名称最多50个字符',
            'goodCategoryId.required' => '分类ID不能为空',
            'goodCategoryId.exists'   => '分类ID不存在',
            'brandId.required'        => '品牌ID不能为空',
            'brandId.exists'          => '品牌ID不存在',
            'virtualType.required'    => '虚实类型不能为空',
            'virtualType.in'          => '虚实类型参数错误',
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
