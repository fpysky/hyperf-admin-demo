<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\Actions\AbstractAction;
use App\AdminRbac\CodeMsg\TokenCode;
use App\AdminRbac\Model\Admin\Admin;
use App\AdminRbac\Request\LoginRequest;
use App\Exception\UnauthorizedException;
use App\Utils\Help;
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

use function Hyperf\Coroutine\go;

#[HyperfServer('http')]
#[Controller]
class LoginAction extends AbstractAction
{
    #[Inject]
    protected Help $help;

    #[Inject]
    protected AuthManager $auth;

    #[Inject]
    protected EventDispatcherInterface $eventDispatcher;

    #[PostMapping(path: '/login/backend')]
    #[Post(path: '/login/backend', summary: '登陆', tags: ['后台管理/账号'])]
    #[RequestBody(content: new JsonContent(
        required: ['username', 'password'],
        properties: [
            new Property(property: 'username', description: '用户名', type: 'string', example: '1888888888'),
            new Property(property: 'password', description: '密码', type: 'string', example: 'admin123456'),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data', 'id'],
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
            throw new UnauthorizedException('账户不存在');
        }

        $encryptPassword = $this->help
            ->encrypPassword($mobile, $password, $admin->getUnixCreatedAt());

        if ($encryptPassword !== $admin->password) {
            throw new UnauthorizedException(
                '账号或密码错误',
                TokenCode::FO_ZE_O_ZE_ZE_O
            );
        }

        if ($admin->status === Admin::STATUS_DISABLED) {
            throw new UnauthorizedException(
                '用户已被禁用',
                TokenCode::FO_ZE_O_ZE_ZE_TH
            );
        }

        $accessToken = $this->auth->guard('sso')->login($admin);

        go(function () use ($admin, $ip) {
            $admin->updateLastLoginInfo($ip);
        });

        return $this->success([
            'id' => $admin->id,
            'token' => "Bearer {$accessToken}",
            'name' => $admin->name,
            'logo' => $admin->logo ?: config('myconfig.adminImg'),
        ]);
    }
}
