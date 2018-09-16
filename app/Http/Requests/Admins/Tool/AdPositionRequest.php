<?php namespace App\Http\Requests\Admins\Tool;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * AdPositionRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update model
 *
 * @author caopei@carnetmotor.com
 *
 */
class AdPositionRequest extends Request
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
            'title' => 'max:100',
            'img'   => 'required',
            'url'   => 'required',
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
            'title.max'    => '轮播图名称最多100个字符',
            'img.required' => '图片不能为空',
            'url.required' => '图片对应url不能为空',
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
