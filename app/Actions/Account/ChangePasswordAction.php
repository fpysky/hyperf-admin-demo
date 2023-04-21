<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\Admin;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Request\Account\ChangePasswordRequest;
use App\Utils\Help;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Di\Annotation\Inject;
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
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class ChangePasswordAction extends AbstractAction
{
    #[Inject]
    protected Help $help;

    #[PostMapping(path: '/system/backend/changePassword')]
    #[Post(path: '/system/backend/changePassword', summary: '管理密码修改', tags: ['后台管理/账号'])]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[RequestBody(content: new JsonContent(
        required: ['id', 'password', 'newPassword','retNewPassword'],
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
    public function handle(ChangePasswordRequest $request): ResponseInterface
    {
        $id = (int) $request->input('id');
        $password = (string) $request->input('password');
        $newPassword = (string) $request->input('newPassword');

        try {
            $admin = Admin::query()->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new UnprocessableEntityException('管理员不存在');
        }

        $unixCreatedAt = $admin->getUnixCreatedAt();
        $encryptPassword = $this->help->encrypPassword($admin->mobile, $password, $unixCreatedAt);

        if ($admin->password !== $encryptPassword) {
            throw new UnprocessableEntityException('原密码错误');
        }

        $encryptNewPassword = $this->help->encrypPassword($admin->mobile, $newPassword, $unixCreatedAt);
        if ($admin->password === $encryptNewPassword) {
            throw new UnprocessableEntityException('原密码与修改密码相同');
        }

        $admin->password = $encryptNewPassword;
        $admin->save();

        return $this->message('密码修改成功');
    }
}
