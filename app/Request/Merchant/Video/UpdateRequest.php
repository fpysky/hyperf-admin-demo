<?php

declare(strict_types=1);

namespace App\Request\Merchant\Video;

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
            'id' => 'required|integer',
            'title' => 'required|max:20',
        ];
    }

    public function messages():array
    {
        return [
            'id.required' => '视频id必填',
            'id.integer' => '视频id为整数',
            'title.required' => '请输入标题',
            'title.max' => '标题最多20字',
        ];
    }

}


