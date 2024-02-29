<?php

declare(strict_types=1);

namespace App\Request\Role;

use Hyperf\Validation\Request\FormRequest;

class SetRuleRequest extends FormRequest
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
            'ruleIds' => 'required|array',
            'roleId' => 'required|integer:strict',
        ];
    }

    public function messages(): array
    {
        return [
            'ruleIds.required' => '权限id数组不能为空',
            'ruleIds.array' => '权限id必须是一个数组',
            'roleId.required' => '角色id不能为空',
        ];
    }
}
