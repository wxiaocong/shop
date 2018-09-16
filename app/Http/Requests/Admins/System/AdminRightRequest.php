<?php namespace App\Http\Requests\Admins\System;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * AdminRightRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update areas
 *
 * @author caopei@carnetmotor.com
 *
 */
class AdminRightRequest extends Request
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
            'categoryId'  => 'required|exists:admin_rights_categories,id',
            'name'        => 'required|max:60',
            'sortNum'     => 'required|integer',
            'showMenu'    => 'required|in:0,1',
            'description' => 'between:0,512',
            'action'      => 'required',
            'url'         => 'required',
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
            'categoryId.required' => '分类ID不能为空',
            'categoryId.exists'   => '分类ID不存在',
            'name.required'       => '权限名不能为空',
            'name.max'            => '权限名最多60个字符',
            'sortNum.required'    => '序列号不能为空',
            'sortNum.integer'     => '序列号只能是数字',
            'showMenu.required'   => '是否菜单不能为空',
            'showMenu.in'         => '是否菜单参数错误',
            'description.between' => '描述不能超过512个字符',
            'action.required'     => 'action不能为空',
            'url.required'        => 'url不能为空',
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
