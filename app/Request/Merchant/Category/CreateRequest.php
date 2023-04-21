<?php

declare(strict_types=1);

namespace App\Request\Merchant\Category;

use Hyperf\Validation\Request\FormRequest;

class CreateRequest extends FormRequest
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
            'parentId' => 'required|integer',
            'categoryName' => 'required|max:10',
            'iconUrl' => 'required',
            'sortOrder' => 'required|integer',
        ];
    }

    public function messages():array
    {
        return [
            'parentId.required' => '父id必填',
            'parentId.integer' => '父id为整数',
            'categoryName.required' => '请输入分类名称',
            'categoryName.max' => '分类名称最多10字',
            'iconUrl.required' => '请上传分类图片',
            'sortOrder.required' => '请输入排序',
            'sortOrder.integer' => '排序为数字',
        ];
    }
}

