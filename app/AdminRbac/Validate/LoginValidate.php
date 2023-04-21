<?php

declare(strict_types=1);

namespace App\AdminRbac\Validate;

class LoginValidate extends BaseValidate
{
    public function check(&$data): string
    {
//        if (! empty($data['password'])) {
//            $data['password'] = $this->help->opensslData($data['password'], 'decrypt');
//        }
        return $this->checkInfo($data, $this->rule(), $this->message());
    }

    private function rule(): array
    {
        return [
            'username' => 'required|regex:' . $this->help->mobilePreg(),
            'password' => 'required|regex:' . $this->help->passwordPreg(),
        ];
    }

    private function message(): array
    {
        return [
            'username.required' => '填写手机号码',
            'username.regex' => '手机号码格式不对',
            'password.required' => '填写密码',
            'password.regex' => '密码格式不对',
        ];
    }
}
