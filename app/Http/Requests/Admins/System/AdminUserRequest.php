<?php namespace App\Http\Requests\Admins\System;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * AdminUserRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update areas
 *
 *
 */
class AdminUserRequest extends Request
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
            'name'     => 'required|max:20',
            'roleId.*' => 'exists:admin_roles,id',
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
            'name.required'   => '用户名不能为空',
            'name.max'        => '用户名最多20个字符',
            'roleId.*.exists' => '角色ID不存在',
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
