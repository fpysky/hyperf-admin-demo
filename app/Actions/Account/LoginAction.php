<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\Admin;
use App\AdminRbac\Request\LoginRequest;
use App\Exception\UnprocessableEntityException;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\RequestBody;
use Hyperf\Swagger\Annotation\Response;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Qbhy\HyperfAuth\AuthManager;

#[HyperfServer('http')]
#[Controller]
class LoginAction extends AbstractAction
{
    #[Inject]
    protected AuthManager $auth;

    #[Inject]
    protected EventDispatcherInterface $eventDispatcher;

    #[PostMapping(path: '/login')]
    #[Post(path: '/login', summary: '登陆', tags: ['后台管理/账号'])]
    #[RequestBody(content: new JsonContent(
        required: ['username', 'password'],
        properties: [
            new Property(property: 'username', description: '用户名', type: 'string', example: '1888888888'),
            new Property(property: 'password', description: '密码', type: 'string', example: 'admin123456'),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(
                property: 'data',
                description: '返回对象',
                required: ['id', 'token', 'name', 'logo'],
                properties: [
                    new Property(property: 'id', description: '管理员id', type: 'integer', example: 1),
                    new Property(property: 'token', description: '登陆凭证', type: 'string', example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd'),
                    new Property(property: 'name', description: '管理员名称', type: 'string', example: '小蜜'),
                    new Property(property: 'logo', description: '管理员logo', type: 'string', example: 'http://aa.com'),
                ],
                type: 'object'
            ),
        ]
    ))]
    public function handle(LoginRequest $request): ResponseInterface
    {
        $mobile = $request->input('username');
        $password = $request->input('password');
        $xRealIp = $request->getHeaderLine('x-real-ip');
        $xForwardedFor = $request->getHeaderLine('x-forwarded-for');
        $ip = $xRealIp ?? $xForwardedFor ?? '';

        try {
            $admin = Admin::query()
                ->where('mobile', $mobile)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            throw new UnprocessableEntityException('账户不存在');
        }

        if (! password_verify($password, $admin->password)) {
            throw new UnprocessableEntityException('账号或密码错误');
        }

        if ($admin->isDisabled()) {
            throw new UnprocessableEntityException('用户已被禁用');
        }

        $accessToken = $this->auth->guard('sso')->login($admin);

        go(function () use ($admin, $ip) {
            $admin->updateLastLoginInfo($ip);
        });

        return $this->success([
            'id' => $admin->id,
            'token' => $accessToken,
            'name' => $admin->name,
            'logo' => $admin->logo ?: config('myconfig.adminImg'),
        ]);
    }
}
