<?php

declare(strict_types=1);

namespace App\Controller\Permission;

use App\Controller\AbstractController;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\Admin;
use App\Request\Account\ChangePasswordRequest;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Swagger\Annotation\HeaderParameter;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\RequestBody;
use Hyperf\Swagger\Annotation\Response;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'api')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class AccountController extends AbstractController
{
    #[PostMapping(path: 'account/changePassword')]
    #[Post(path: 'account/changePassword', summary: '管理密码修改', tags: ['后台管理/账号'])]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[RequestBody(content: new JsonContent(
        required: ['id', 'password', 'newPassword', 'retNewPassword'],
        properties: [
            new Property(property: 'id', description: '管理员id', type: 'integer', example: 1),
            new Property(property: 'password', description: '原密码', type: 'string', example: '4343434'),
            new Property(property: 'newPassword', description: '重置密码', type: 'string', example: 'sdsds'),
            new Property(property: 'retNewPassword', description: '确认重置密码', type: 'string', example: 'sdsds'),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '密码修改成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function changePassword(ChangePasswordRequest $request): ResponseInterface
    {
        $id = $request->integer('id');
        $password = $request->string('password');
        $newPassword = $request->string('newPassword');

        $admin = Admin::findFromCacheOrFail($id);

        if (! password_verify($password, $admin->password)) {
            throw new UnprocessableEntityException('原密码错误');
        }

        if (password_verify($newPassword, $admin->password)) {
            throw new UnprocessableEntityException('原密码与修改密码相同');
        }

        $admin->password = encryptPassword($newPassword);
        $admin->save();

        return $this->message('密码修改成功');
    }
}
