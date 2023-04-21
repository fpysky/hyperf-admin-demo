<?php

declare(strict_types=1);

namespace App\Request\Merchant\Image;

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
            'merchantId' => 'required|integer',
            'imageUrl' => 'required|max:255',
        ];
    }

    public function messages():array
    {
        return [
            'merchantId.required' => '商家id必填',
            'merchantId.integer' => '商家id为整数',
            'imageUrl.required' => '请设置图片路径',
            'imageUrl.max' => '图片路径太长超过255',
        ];
    }
}

