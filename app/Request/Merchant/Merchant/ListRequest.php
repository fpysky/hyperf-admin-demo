<?php

declare(strict_types=1);

namespace App\Request\Merchant\Merchant;

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
            'merchantName' => 'max:20',
            //'categoryId' => 'required|integer',
            //'businessCircleId' => 'required',
            //'status' => 'required',
            'minSalesCount' => 'integer',
            'maxSalesCount' => 'integer',
            'startCreateTime' => 'date_format:Y-m-d',
            'endCreateTime' => 'date_format:Y-m-d',
        ];
    }

    public function messages():array
    {
        return [
            'page.integer' => '页码必须为整数',
            'pageSize.integer' => '每页条数必须为整数',
            'merchantName.max' => '商家名称最多20字',
            'minSalesCount.integer' => '销量必须为整数',
            'maxSalesCount.integer' => '销量必须为整数',
            'startCreateTime.date_format' => '入驻时间格式错误',
            'endCreateTime.date_format' => '入驻时间格式错误',
        ];
    }
}


