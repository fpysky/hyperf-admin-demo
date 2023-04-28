<?php

declare(strict_types=1);

namespace App\AdminRbac\Request;

class AdminUpdateRequest extends AdminStoreRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = parent::rules();

        return array_merge($rules, [
            'id' => 'required|integer',
        ]);
    }

    public function messages(): array
    {
        $messages = parent::messages();

        return array_merge($messages, [
            'id.required' => 'ID不存在',
            'id.integer' => 'ID格式错误',
        ]);
    }
}
