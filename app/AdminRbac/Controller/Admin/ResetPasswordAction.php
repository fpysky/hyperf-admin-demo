<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Admin;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\Admin;
use App\AdminRbac\Request\ResetPasswordRequest;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PatchMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Patch;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\RequestBody;
use Hyperf\Swagger\Annotation\Response;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'admin')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class ResetPasswordAction extends AbstractAction
{
    #[PatchMapping(path: 'resetPassword')]
    #[Patch(path: '/admin/resetPassword', summary: '重置管理员密码', tags: ['后台管理/系统权限管理'])]
    #[RequestBody(content: new JsonContent(
        required: ['id', 'password'],
        properties: [
            new Property(property: 'id', description: '用户id', type: 'integer', example: 1),
            new Property(property: 'password', description: '重置密码', type: 'string', example: 'admin123456'),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '管理员密码重置成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function resetPassword(ResetPasswordRequest $request): ResponseInterface
    {
        $id = (int) $request->input('id');
        $password = (string) $request->input('password');

        $admin = Admin::findFromCacheOrFail($id);

        if ($admin->isSuper()) {
            throw new UnprocessableEntityException('超级管理员禁止重置密码');
        }

        $admin->password = Admin::encryptPassword($password);
        $admin->save();

        return $this->message('管理员密码重置成功');
    }
}
