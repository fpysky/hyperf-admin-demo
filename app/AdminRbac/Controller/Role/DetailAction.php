<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Role;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Role\Role;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Resource\Role\RoleResource;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\PathParameter;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Response;
use Hyperf\Swagger\Annotation\Schema;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'role')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class DetailAction extends AbstractAction
{
    #[GetMapping(path: '{id:\d+}')]
    #[Get(path: '/role/{id}', summary: '角色详情', tags: ['后台管理/系统管理/角色'])]
    #[PathParameter(name: 'id', description: '角色id', required: true, schema: new Schema(type: 'integer'), example: 1)]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(
                property: 'data',
                description: '返回对象',
                required: ['id', 'name', 'desc', 'sort', 'status', 'createdAt', 'updatedAt'],
                properties: [
                    new Property(property: 'id', description: '', type: 'integer', example: 1),
                    new Property(property: 'name', description: '', type: 'string', example: ''),
                    new Property(property: 'desc', description: '', type: 'string', example: ''),
                    new Property(property: 'sort', description: '', type: 'integer', example: 1),
                    new Property(property: 'status', description: '', type: 'integer', example: 1),
                    new Property(property: 'createdAt', description: '', type: 'string', example: '2023-11-11 11:11:11'),
                    new Property(property: 'updatedAt', description: '', type: 'string', example: '2023-11-11 11:11:11'),
                ],
                type: 'object'
            ),
        ]
    ))]
    public function detail(int $id): ResponseInterface
    {
        $role = Role::findFromCacheOrFail($id);

        return $this->success(new RoleResource($role));
    }
}
