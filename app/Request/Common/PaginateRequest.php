<?php

declare(strict_types=1);

namespace App\Request\Common;

use Hyperf\Validation\Request\FormRequest;

class PaginateRequest extends FormRequest
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
            'page' => 'numeric',
            'pageSize' => 'numeric',
        ];
    }

    public function messages():array
    {
        return [
            'page.numeric' => '页码必须是数值型',
            'pageSize.numeric' => '每页显示条数必须是数值型',
        ];
    }
}
