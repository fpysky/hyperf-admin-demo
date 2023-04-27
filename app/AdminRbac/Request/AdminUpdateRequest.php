<?php

declare(strict_types=1);

namespace App\AdminRbac\Request;

class AdminUpdateRequest extends AdminStoreRequest
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

        $rules['id'] = 'required|integer';

        return $rules;
    }

    public function messages(): array
    {
        $messages = parent::messages();

        $messages['id.required'] = 'ID不存在';
        $messages['id.integer'] = 'ID格式错误';

        return $messages;
    }
}
