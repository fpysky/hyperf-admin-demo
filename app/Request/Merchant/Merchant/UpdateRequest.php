<?php

declare(strict_types=1);

namespace App\Request\Merchant\Merchant;

use Hyperf\Validation\Request\FormRequest;

class UpdateRequest extends FormRequest
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
            'phone' => 'integer|min:13000000000|max:19999999999',
            'merchantName' => 'max:20',
            'recommendation' => 'max:20',
            'businessHourType' => 'integer|between:0,2',
            'contact' => 'max:50',
            'areaId' => 'integer',
            'address' => 'max:255',
        ];
    }

    public function messages():array
    {
        return [
            'phone.integer' => '商家账户必须为11位手机号格式',
            'phone.min' => '商家账户必须为11位手机号格式',
            'phone.max' => '商家账户必须为11位手机号格式',
            'merchantName.max' => '商家名称最多20字',
            'recommendation.max' => '推荐语最多20个字',
            'businessHourType.integer' => '营业时间类型错误',
            'businessHourType.between' => '营业时间类型错误',
            'contact.max' => '联系方式超过最大限制',
            'address.max' => '详细地址最大长度255',
            'areaId.integer' => '地区格式错误',
        ];
    }
}


