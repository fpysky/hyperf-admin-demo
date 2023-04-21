<?php

declare(strict_types=1);

namespace HyperfTest\Cases;

trait Resources
{
    protected int $statusCodeOk = 200;

    protected int $codeOk = 200000;

    protected bool $debug = false;

    protected bool $login = false;

    protected string $loginPhone = '18976327749';

    protected string $loginPassword = '123456';

    protected array $getToken = [
        'method' => 'post',
        'path' => '/talent/account/token',
        'host' => 'http://172.29.151.140',
    ];
}
