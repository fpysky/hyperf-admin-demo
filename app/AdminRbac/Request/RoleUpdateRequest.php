<?php

declare(strict_types=1);

namespace App\AdminRbac\Request;

class RoleUpdateRequest extends RoleStoreRequest
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
        $rules = parent::rules();
        $id = (int) $this->input('id');

        return array_merge($rules, [
            'id' => 'required|integer',
            'name' => "required|max:10|unique:role,name,{$id},id",
        ]);
    }

    public function messages(): array
    {
        $messages = parent::messages();

        return array_merge($messages, [
            'id.required' => 'id不能为空',
            'id.integer' => 'id必须为整型',
        ]);
    }
}
