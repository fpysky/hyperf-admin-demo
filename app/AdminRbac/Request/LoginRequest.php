<?php

declare(strict_types=1);

namespace App\AdminRbac\Request;

use Hyperf\Validation\Request\FormRequest;

class LoginRequest extends FormRequest
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
            'username' => 'required|string|regex:/^1[3456789]\d{9}$/',
            'password' => 'required|string|regex:/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => '填写手机号码',
            'username.regex' => '手机号码格式不对',
            'password.required' => '填写密码',
            'password.regex' => '密码格式不对',
        ];
    }
}
