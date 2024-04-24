<?php

declare(strict_types=1);

namespace App\Request\Account;

use App\Request\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'id' => 'required|integer:strict',
            'password' => 'required|string',
            'newPassword' => 'required|string|regex:/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/',
            'retNewPassword' => 'required|string|same:newPassword',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID不存在',
            'password.required' => '原密码不能为空',
            'newPassword.required' => '新密码不能为空',
            'newPassword.regex' => '登录密码必须同时含有数字和字母，且长度要在6-16位之间',
            'retNewPassword.required' => '确认密码不能为空',
            'retNewPassword.same' => '确认密码与新密码不一致',
        ];
    }
}
