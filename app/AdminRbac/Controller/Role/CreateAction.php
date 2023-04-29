<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Role;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Role\Role;
use App\AdminRbac\Request\RoleStoreRequest;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
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
class CreateAction extends AbstractAction
{
    #[PostMapping(path: '/role')]
    #[Post(path: '/role', summary: '添加角色', tags: ['后台管理/系统管理/角色'])]
    #[RequestBody(content: new JsonContent(
        required: ['name', 'desc', 'status', 'sort'],
        properties: [
            new Property(property: 'name', description: '角色名', type: 'string', example: ''),
            new Property(property: 'desc', description: '描述', type: 'string', example: ''),
            new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: 1),
            new Property(property: 'sort', description: '排序', type: 'integer', example: 1),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '角色添加成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function handle(RoleStoreRequest $request): ResponseInterface
    {
        $name = $request->input('name');
        $desc = $request->input('desc');
        $sort = (int) $request->input('sort');
        $status = (int) $request->input('status');

        $role = new Role();
        $role->name = $name;
        $role->desc = $desc;
        $role->sort = $sort;
        $role->status = $status;
        $role->save();

        return $this->message('角色添加成功');
    }
}
