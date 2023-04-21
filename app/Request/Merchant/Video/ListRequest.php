<?php

declare(strict_types=1);

namespace App\Request\Merchant\Video;

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
            'merchantId' => 'required|integer',
            'page' => 'integer',
            'pageSize' => 'integer',
        ];
    }

    public function messages():array
    {
        return [
            'merchantId.required' => '商家id必填',
            'merchantId.integer' => '商家id为整数',
            'page.integer' => '页码必须为整数',
            'pageSize.integer' => '每页条数必须为整数',
        ];
    }
}


