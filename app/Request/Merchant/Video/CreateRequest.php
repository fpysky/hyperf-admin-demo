<?php

declare(strict_types=1);

namespace App\Request\Merchant\Video;

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
            'title' => 'required|max:20',
            'videoUrl' => 'required|max:255',
            'coverUrl' => 'required|max:255',
        ];
    }

    public function messages():array
    {
        return [
            'merchantId.required' => '商家id必填',
            'merchantId.integer' => '商家id为整数',
            'title.required' => '请输入标题',
            'title.max' => '标题最多20字',
            'coverUrl.required' => '请设置缩略图图片路径',
            'coverUrl.max' => '图片路径太长超过255',
            'videoUrl.required' => '请设置视频路径',
            'videoUrl.max' => '视频路径太长超过255',
        ];
    }
}

