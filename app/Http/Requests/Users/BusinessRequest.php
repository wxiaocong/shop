<?php namespace App\Http\Requests\Users;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;

/**
 *--------------------------------------------------------------------------
 * UsersRequest Request
 *--------------------------------------------------------------------------
 *
 * This Request validate store/update users
 *
 * @author wangcong@carnetmotor.com
 *
 */
class BusinessRequest extends Request
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
            'company_name'      =>'required|max:200',
            'company_address'   =>'required|max:200',
            'company_province'  =>'required|integer',
            'company_city'      =>'required|integer',
            'company_area'      =>'required|integer',
            'shop_site'         =>'required|integer',
            'business_license'  =>'required|max:200',
            'doorhead_photo'    =>'required|max:200'
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
            'company_name.required' => '公司名称必填',
            'company_name.max'  => '公司名称最多200个字符',
            'company_address.required' => '详情地址必填',
            'company_address.max'  => '详情地址最多200个字符',
            'company_province.required' => '所属区域必填',
            'company_city.required' => '所属区域必填',
            'company_area.required' => '所属区域必填',
            'company_province.integer'=> '省格式错误',
            'company_city.integer'  => '市格式错误',
            'company_area.integer'  => '区格式错误',
            'shop_site.required' => '店铺工位必填',
            'shop_site.integer'  => '店铺工位格式错误',
            'business_license.required' => '营业执照必填',
            'business_license.max' => '营业执照图片地址过长',
            'doorhead_photo.required' => '门头照片必填',
            'doorhead_photo.max' => '门头照片图片地址过长',
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
