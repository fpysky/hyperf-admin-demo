<?php

declare(strict_types=1);

namespace App\Request\Merchant\Merchant;

use Hyperf\Validation\Request\FormRequest;

class StatusRequest extends FormRequest
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
            'status' => 'required|integer|between:1,2',
        ];
    }

    public function messages():array
    {
        return [
            'id.required' => '商家id必须输入',
            'id.integer' => '商家id为整数',
            'status.required' => '状态必须输入',
            'status.integer' => '状态为整数',
            'status.between' => '状态参数错误'
        ];
    }
}



