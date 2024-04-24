<?php

declare(strict_types=1);

namespace App\Request;

class AdminUpdateRequest extends AdminStoreRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = parent::rules();
        $id = (int) $this->input('id');

        return array_merge($rules, [
            'id' => 'required|integer',
            'name' => "required|max:10|unique:admin,name,{$id},id",
            'mobile' => "required|unique:admin,mobile,{$id},id|regex:/^1[3456789]\\d{9}$/",
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
