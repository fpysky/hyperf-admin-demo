<?php

declare(strict_types=1);

namespace App\AdminRbac\Request;

class RuleUpdateRequest extends RuleStoreRequest
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

        return array_merge($rules,[
            'id' => 'required|integer',
        ]);
    }

    public function messages(): array
    {
        $messages = parent::messages();

        return array_merge($messages,[
            'id.required' => 'id不存在',
            'id.integer' => 'id格式不对',
        ]);
    }
}
