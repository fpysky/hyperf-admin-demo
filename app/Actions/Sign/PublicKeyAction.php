<?php

declare(strict_types=1);

namespace App\Actions\Sign;

use App\Actions\AbstractAction;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
class PublicKeyAction extends AbstractAction
{
    #[GetMapping(path: '/jyOpenApi/secure/rsa/publicKey')]
    public function handle(): ResponseInterface
    {
        return $this->item('-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAq2ZBPdYEIYQ4JjEo13m5
0Wg/4AyUu6m82oXqw96AC4cte10QfGcp218U7P1Qs9eQuJLcRWpc+ziN0KNdA30l
mEo1qybkOOzvACGXP4yTCfCLdoQhgU5dGeRmTeqqNZ8BOp9Iuvpc/w7CLg1gp6Kn
wXApTc0x60MkzFPtm/H53b//K+k0dK9mJrOMG+BKUAPjfDbkQA4V5YpJyw833a9/
Zm1gFuGuGLpHqIvI/c13/9Evv/7/LQd2VjZVTwehktQD8Y8vqulLLi0rmmeR+HHr
qieazbjMrz9jho1ZOMD7O3VdhFc4o6Y8nig4iRh206ETn574trU/y5yYQHFpgpYN
0wIDAQAB
-----END PUBLIC KEY-----');
    }
}
