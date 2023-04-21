<?php

declare(strict_types=1);

namespace App\AdminRbac\Validate;

class ManagerValidate extends BaseValidate
{
    public function check($data): string
    {
        return $this->checkInfo($data, $this->rule(), $this->message());
    }

    private function rule(): array
    {
        return [
            'name' => 'required|max:10',
            'mobile' => 'required|regex:' . $this->help->mobilePreg(),
            'email' => 'required|email',
        ];
    }

    private function message(): array
    {
        return [
            'name.required' => '填写姓名',
            'name.max' => '姓名长度不能超过10个字符',
            'mobile.required' => '填写手机号码',
            'mobile.regex' => '手机号码格式错误',
            'email.required' => '填写邮箱',
            'email.email' => '邮箱格式错误',
        ];
    }
}
