<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Admin;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\Admin;
use App\AdminRbac\Request\UpStatusRequest;
use App\Exception\UnprocessableEntityException;
use App\Extend\Auth\AuthManager;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Items;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Put;
use Hyperf\Swagger\Annotation\RequestBody;
use Hyperf\Swagger\Annotation\Response;
use Psr\Http\Message\ResponseInterface;
use Qbhy\SimpleJwt\Exceptions\InvalidTokenException;
use Qbhy\SimpleJwt\Exceptions\SignatureException;
use Qbhy\SimpleJwt\Exceptions\TokenExpiredException;

#[HyperfServer('http')]
#[Controller(prefix: 'admin')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class UpStatusAction extends AbstractAction
{
    #[Inject]
    protected AuthManager $auth;

    /**
     * @throws \RedisException
     * @throws SignatureException
     * @throws InvalidTokenException
     * @throws TokenExpiredException
     */
    #[PutMapping(path: '/status')]
    #[Put(path: '/status', summary: '修改管理员状态', tags: ['后台管理/系统管理/管理员'])]
    #[RequestBody(content: new JsonContent(
        required: ['ids', 'status'],
        properties: [
            new Property(property: 'ids', description: '管理员id数组', type: 'array', items: new Items(type: 'integer')),
            new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: 1),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '管理员启用成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function handle(UpStatusRequest $request): ResponseInterface
    {
        $ids = (array) $request->input('ids');
        $status = (int) $request->input('status');

        if (Admin::hasSuperAdmin($ids)) {
            throw new UnprocessableEntityException('不能禁用超级管理员');
        }

        Admin::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);

        // 禁用时，强制退出
        if ($status === Admin::STATUS_DISABLED) {
            $this->auth->batchLogoutByAdmin($ids);
        }

        $action = $status === Admin::STATUS_ENABLE ? '启用' : '禁用';

        return $this->success("管理员{$action}成功");
    }
}
