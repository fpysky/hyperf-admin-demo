<?php

declare(strict_types=1);

namespace App\AdminRbac\Validate;

class WebsiteValidate extends BaseValidate
{
    public function check($data): string
    {
        return $this->checkInfo($data, $this->rule(), $this->message());
    }

    private function rule(): array
    {
        return [
            'id' => 'required|integer',
            'domain' => 'required|max:30',
            'name' => 'required|max:10',
            'slogan' => 'required|max:30',
            'desc' => 'required|max:255',
            'number' => 'required|max:17',
            'company' => 'required|max:20',
            'company_alias' => 'required|max:10',
            'address' => 'required|max:50',
        ];
    }

    private function message(): array
    {
        return [
            'id.required' => 'ID不存在',
            'id.integer' => 'ID格式不对',
            'domain.required' => '填写网站域名',
            'domain.max' => '网站域名不能超过30个字符',
            'name.required' => '填写网站名称',
            'name.max' => '网站名称不能超过10个字符',
            'slogan.required' => '填写网站SLOGAN',
            'slogan.max' => '网站SLOGAN不能超过30个字符',
            'desc.required' => '填写网站描述',
            'desc.max' => '网站描述不能超过255个字符',
            'number.required' => '填写ICP备案号',
            'number.max' => 'ICP备案号不能超过17个字符',
            'company.required' => '填写公司名称',
            'company.max' => '公司名称不能超过20个字符',
            'company_alias.required' => '填写公司简称',
            'company_alias.max' => '公司简称不能超过10个字符',
            'address.required' => '填写公司地址',
            'address.max' => '公司地址不能超过50个字符',
        ];
    }
}
