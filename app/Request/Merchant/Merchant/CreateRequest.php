<?php

declare(strict_types=1);

namespace App\Request\Merchant\Merchant;

use Hyperf\Validation\Request\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'phone' => 'required|integer|min:13000000000|max:19999999999',
            'merchantName' => 'required|max:20',
            'categoryId' => 'required',
            'coverUrl' => 'required',
            'recommendation' => 'max:20',
            'businessHourType' => 'required|integer|between:0,2',
            'businessHours' => 'required',
            'contact' => 'required|max:50',
            'areaId' => 'required|integer',
            'address' => 'required|max:255',
            'lnglat' => 'required',
            'businessLicenseUrl' => 'required',
        ];
    }

    public function messages():array
    {
        return [
            'phone.required' => '商家账户必填',
            'phone.integer' => '商家账户必须为11位手机号格式',
            'phone.min' => '商家账户必须为11位手机号格式',
            'phone.max' => '商家账户必须为11位手机号格式',
            'merchantName.required' => '请输入商家名称',
            'merchantName.max' => '商家名称最多20字',
            'categoryId.required' => '请选择分类',
            'coverUrl.required' => '请上传商家封面',
            'recommendation.max' => '推荐语最多20个字',
            'businessHourType.required' => '请选择营业时间类型',
            'businessHourType.integer' => '营业时间类型错误',
            'businessHourType.between' => '营业时间类型错误',
            'businessHours.required' => '请输入营业时间',
            'contact.required' => '请输入联系方式',
            'contact.max' => '联系方式超过最大限制',
            'address.required' => '请输入商家地址',
            'address.max' => '详细地址最大长度255',
            'areaId.required' => '请选择地区',
            'areaId.integer' => '地区格式错误',
            'lnglat.required' => '请输入经纬度',
            'businessLicenseUrl.required' => '请上传营业执照',
        ];
    }
}

