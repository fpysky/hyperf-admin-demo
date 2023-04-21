<?php

declare(strict_types=1);

namespace App\AdminRbac\Validate;

class PasswordValidate extends BaseValidate
{
    public function check(&$data): string
    {
        if (! empty($data['password'])) {
            $data['password'] = $this->help->opensslData($data['password'], 'decrypt');
        }
        if (! empty($data['retpassword'])) {
            $data['retpassword'] = $this->help->opensslData($data['retpassword'], 'decrypt');
        }
        return $this->checkInfo($data, $this->rule(), $this->message());
    }

    private function rule(): array
    {
        return [
            'id' => 'required|integer',
            'password' => 'required|regex:' . $this->help->passwordPreg(),
            'retpassword' => 'required|same:password',
        ];
    }

    private function message(): array
    {
        return [
            'id.required' => 'ID不存在',
            'id.integer' => 'ID错误',
            'password.required' => '填写密码',
            'password.regex' => '登录密码必须同时含有数字和字母，且长度要在6-16位之间',
            'retpassword.required' => '填写确认密码',
            'retpassword.same' => '密码不一致',
        ];
    }
}
