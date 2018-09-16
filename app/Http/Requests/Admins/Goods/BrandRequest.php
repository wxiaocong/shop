<?php namespace App\Http\Requests\Admins\Goods;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * BrandRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update brand
 *
 * @author wangcong@carnetmotor.com
 *
 */
class BrandRequest extends Request
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
            'logo_cname'      => 'max:250',
            'logo_ename'      => 'max:250',
            'short_name'      => 'required|max:250',
            'sort'      => 'integer',
            'state'     => 'required|in:0,1'
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
            'logo_cname.max'     => '品牌中文名称最多250个字符',
            'logo_ename.max'     => '品牌英文名称最多250个字符',
            'short_name.required'=> '请输入简称',
            'short_name.max'     => '简称最多250个字符',
            'sort.integer'       => '排序错误',
            'state.required'     => '请选择状态',
            'state.in'           => '状态错误',
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
