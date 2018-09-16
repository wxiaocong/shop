<?php namespace App\Http\Requests\Admins\Market;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * GoodSpeedRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update model
 *
 * @author caopei@carnetmotor.com
 *
 */
class GoodSpeedRequest extends Request
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
            'name'      => 'required|max:100',
            'startDate' => 'required',
            'endDate'   => 'required',
            'isClose'   => 'required|in:0,1',
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
            'name.required'      => '请输入活动名称',
            'name.max'           => '活动名称最多100个字符',
            'startDate.required' => '活动开始时间不能为空',
            'endDate.required'   => '活动结束时间不能为空',
            'isClose.required'   => '是否开启不能为空',
            'isClose.in'         => '是否开启参数错误',
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
