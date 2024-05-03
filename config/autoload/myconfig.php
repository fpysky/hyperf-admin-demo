<?php

declare(strict_types=1);

$baidu = include BASE_PATH . '/config/autoload/custom/baidu.php';
return [
    'baidu' => $baidu, //百度相关配置
    'moduleName' => 'admin', //后台模块名称
    'encryKey' => '&^*%(#)@!~', //对称加解密key
    'salt'=>'&*()^%#@!~',//密码用的盐
    'adminImg'=>'https://wpimg.wallstcn.com/69a1c46c-eb1c-4b46-8bd4-e9e686ef5251.png',//默认头像
    //api接口请求
    'requireToken'=>[
        'name' => 'token',//名称
        'salt'=>'~!)(@*#&%^', //token盐
        'expireTime'=>60 * 60 * 24 * 7, //token过期时间
        'rule'=>'admin-login-token-by-uid-'
    ],
];
