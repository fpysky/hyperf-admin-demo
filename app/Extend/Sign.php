<?php

declare(strict_types=1);

namespace App\Extend;

class Sign
{
    public function make():string
    {

        /**
         * 1.检查时间戳是否过期
         * 2.所有参数，取参数名（key），按照ASCII码值进行升序排序（字典序）
         * 3.拼接为url query的形式
         * 4.拼接Authorization
         */
        $sign = '';

        $nonceStr = '';
        $timestamp = '';
        $parameters = [];
        $parameterKeys = array_keys($parameters);
        $parameterValues = array_values($parameters);

        ksort($parameters);
        $authorization = '';


        $exceptSignParamsQuery = http_build_query($parameters);


        return $sign;
    }
}
