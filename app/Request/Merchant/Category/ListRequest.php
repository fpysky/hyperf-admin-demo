<?php

declare(strict_types=1);

namespace App\Request\Merchant\Category;

use Hyperf\Validation\Request\FormRequest;

class ListRequest extends FormRequest
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
            'page' => 'integer',
            'pageSize' => 'integer',
            'categoryName' => 'max:10',
        ];
    }

    public function messages():array
    {
        return [
            'page.integer' => '页码必须为整数',
            'pageSize.integer' => '每页条数必须为整数',
            'categoryName.max' => '分类名称最多10字',
        ];
    }
}


