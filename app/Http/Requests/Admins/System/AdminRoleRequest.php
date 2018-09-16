<?php namespace App\Http\Requests\Admins\System;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * AdminRoleRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update areas
 *
 * @author caopei@carnetmotor.com
 *
 */
class AdminRoleRequest extends Request
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
            'name'        => 'required|max:60',
            'description' => 'max:512',
            'rightId.*'   => 'exists:admin_rights,id',
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
            'name.required'    => '角色名不能为空',
            'name.max'         => '角色名最多60个字符',
            'description.max'  => '描述最多512个字符',
            'rightId.*.exists' => '权限ID不存在',
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
