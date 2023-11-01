<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\AdminRole;
use App\AdminRbac\Model\Role\Role;
use App\AdminRbac\Request\RoleStoreRequest;
use App\AdminRbac\Request\RoleUpdateRequest;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Request\Role\SetRuleRequest;
use App\Request\Role\UpStatusRequest;
use App\Resource\Role\RoleResource;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PatchMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Items;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Patch;
use Hyperf\Swagger\Annotation\PathParameter;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Put;
use Hyperf\Swagger\Annotation\QueryParameter;
use Hyperf\Swagger\Annotation\RequestBody;
use Hyperf\Swagger\Annotation\Response;
use Hyperf\Swagger\Annotation\Schema;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class RoleController extends AbstractAction
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
    public function create(RoleStoreRequest $request): ResponseInterface
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

    /**
     * @throws \Exception
     */
    #[DeleteMapping(path: '/role')]
    #[Delete(path: '/role', summary: '角色删除', tags: ['后台管理/系统管理/角色'])]
    #[PathParameter(name: 'ids', description: '管理员id集合', required: true, schema: new Schema(type: 'string'), example: '1,2')]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '角色删除成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function destroy(): ResponseInterface
    {
        $ids = (array) $this->request->input('ids', []);

        $hasAdminRole = AdminRole::query()
            ->whereIn('role_id', $ids)
            ->exists();

        if ($hasAdminRole) {
            throw new UnprocessableEntityException('有管理员绑定此角色，请解绑后再操作');
        }

        Role::query()
            ->whereIn('id', $ids)
            ->delete();

        return $this->message('角色删除成功');
    }

    #[GetMapping(path: '/role/{id:\d+}')]
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

    #[GetMapping(path: '/role')]
    #[Get(path: '/role', summary: '角色列表', tags: ['后台管理/系统管理/角色'])]
    #[QueryParameter(name: 'page', description: '页码', required: false, schema: new Schema(type: 'integer'))]
    #[QueryParameter(name: 'pageSize', description: '每页显示条数', required: false, schema: new Schema(type: 'integer'))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(
                property: 'data',
                description: '返回对象',
                required: ['total', 'list'],
                properties: [
                    new Property(property: 'total', description: '数据总数', type: 'integer', example: 100),
                    new Property(
                        property: 'list',
                        description: '',
                        type: 'array',
                        items: new Items(
                            required: ['id', 'name', 'desc', 'sort', 'status', 'createdAt', 'updatedAt'],
                            properties: [
                                new Property(property: 'id', description: 'id', type: 'integer', example: 1),
                                new Property(property: 'name', description: '名称', type: 'string', example: ''),
                                new Property(property: 'desc', description: '描述', type: 'string', example: ''),
                                new Property(property: 'sort', description: '排序', type: 'integer', example: 1),
                                new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: 1),
                                new Property(property: 'createdAt', description: '创建时间', type: 'string', example: '2023-11-11 11:11:11'),
                                new Property(property: 'updatedAt', description: '更新时间', type: 'string', example: '2023-11-11 11:11:11'),
                            ]
                        )
                    ),
                ],
                type: 'object'
            ),
        ]
    ))]
    public function index(): ResponseInterface
    {
        $pageSize = (int) $this->request->input('pageSize', 15);

        $paginator = Role::query()
            ->orderBy('sort')
            ->orderByDesc('id')
            ->paginate($pageSize);

        return $this->success([
            'total' => $paginator->total(),
            'list' => RoleResource::collection($paginator),
        ]);
    }

    #[GetMapping(path: '/role/selectData')]
    #[Get(path: '/role/selectData', summary: '角色下拉数据', tags: ['后台管理/系统管理/角色'])]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(
                property: 'data',
                description: '',
                type: 'array',
                items: new Items(
                    required: ['id', 'name'],
                    properties: [
                        new Property(property: 'id', description: '', type: 'integer', example: 1),
                        new Property(property: 'name', description: '名称', type: 'string', example: ''),
                    ]
                )
            ),
        ]
    ))]
    public function selectData(): ResponseInterface
    {
        $list = Role::query()
            ->select(['id', 'name'])
            ->get();

        return $this->success($list);
    }

    /**
     * @throws \Exception
     */
    #[PostMapping(path: '/role/setRule')]
    #[Post(path: '/role/setRule', summary: '角色设置权限', tags: ['后台管理/系统管理/角色'])]
    #[RequestBody(content: new JsonContent(
        required: ['ruleIds', 'roleId'],
        properties: [
            new Property(property: 'ruleIds', description: '权限id数组', type: 'array', example: [1, 2]),
            new Property(property: 'roleId', description: '角色id', type: 'integer', example: 1),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '权限分配成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function handle(SetRuleRequest $request): ResponseInterface
    {
        $ruleIds = (array) $request->input('ruleIds');
        $roleId = (int) $request->input('roleId');

        Role::findFromCacheOrFail($roleId)
            ->setRule($ruleIds);

        return $this->message('权限分配成功');
    }

    #[PutMapping(path: '/role')]
    #[Put(path: '/role', summary: '修改角色', tags: ['后台管理/系统管理/角色'])]
    #[RequestBody(content: new JsonContent(
        required: ['id', 'name', 'desc', 'status', 'sort'],
        properties: [
            new Property(property: 'id', description: '角色id', type: 'integer', example: 1),
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
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '管理员编辑成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function update(RoleUpdateRequest $request): ResponseInterface
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $desc = $request->input('desc');
        $sort = (int) $request->input('sort');
        $status = (int) $request->input('status');

        $role = Role::findFromCacheOrFail($id);

        $role->name = $name;
        $role->desc = $desc;
        $role->sort = $sort;
        $role->status = $status;
        $role->save();

        return $this->message('角色编辑成功');
    }

    #[PatchMapping(path: '/role/status')]
    #[Patch(path: '/role/status', summary: '修改角色状态', tags: ['后台管理/系统管理/角色'])]
    #[RequestBody(content: new JsonContent(
        required: ['ids', 'status'],
        properties: [
            new Property(property: 'ids', description: '角色id数组', type: 'array', items: new Items(type: 'integer')),
            new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: 1),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '角色启用成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function changeStatus(UpStatusRequest $request): ResponseInterface
    {
        $ids = (array) $request->input('ids');
        $status = (int) $request->input('status');

        Role::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);

        $action = $status === Role::STATUS_ENABLE ? '启用' : '禁用';

        return $this->success("角色{$action}成功");
    }
}
