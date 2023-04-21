<?php

declare(strict_types=1);

namespace App\Request\BusinessCircle;

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
            'areaId' => 'required|numeric',
            'name' => 'required',
            'sort' => 'required|numeric',
        ];
    }

    public function messages():array
    {
        return [
            'areaId.required' => '地区id不能为空',
            'areaId.numeric' => '地区id必须是数值型',
            'name.required' => '商圈名称不能为空',
            'sort.required' => '排序不能为空',
            'sort.numeric' => '排序必须是数值型',
        ];
    }
}
