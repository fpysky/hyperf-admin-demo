<?php

declare(strict_types=1);

namespace App\AdminRbac\Request;

use Hyperf\Validation\Request\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'id' => 'required|integer',
            'password' => 'required|regex:/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/',
        ];
    }

    public function messages():array
    {
        return [
            'id.required' => 'ID不存在',
            'id.integer' => 'ID错误',
            'password.required' => '填写密码',
            'password.regex' => '登录密码必须同时含有数字和字母，且长度要在6-16位之间',
        ];
    }
}
