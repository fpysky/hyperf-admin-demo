<?php

declare(strict_types=1);

namespace App\Request\Scenario;

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
            'id' => 'required|numeric',
            'title' => 'required|between:1,20',
            'subtitle' => 'required|between:1,20',
            'coverUrl' => 'required',
            'merchantIds' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'id不能为空',
            'id.numeric' => 'id必须是数值型',
            'title.required' => '标题不能为空',
            'title.between' => '标题字数必须在1～20个',
            'subtitle.required' => '副标题不能为空',
            'subtitle.between' => '副标题字数必须在1～20个',
            'coverUrl.required' => '封面不能为空',
            'merchantIds.required' => '关联商家不能为空',
            'merchantIds.string' => '关联商家必须是字符串',
        ];
    }
}
