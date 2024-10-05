<?php

declare(strict_types=1);

namespace App\Controller;

use App\Annotation\Permission;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\Dto\RoleDto;
use App\Model\Role;
use App\Request\Role\SetRuleRequest;
use App\Request\Role\UpStatusRequest;
use App\Request\RoleStoreRequest;
use App\Request\RoleUpdateRequest;
use App\Resource\Role\RoleResource;
use App\Service\RoleService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PatchMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'api')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class RoleController extends AbstractController
{
    #[Inject]
    protected RoleService $roleService;

    #[PostMapping(path: 'role')]
    #[Permission(name: '添加角色', module: '系统管理/角色管理', hasButton: true)]
    public function create(RoleStoreRequest $request): ResponseInterface
    {
        $dto = $request->makeDto(RoleDto::class);
        $this->roleService->create($dto);

        return $this->message('角色添加成功');
    }

    #[DeleteMapping(path: 'role')]
    #[Permission(name: '删除角色', module: '系统管理/角色管理', hasButton: true)]
    public function destroy(): ResponseInterface
    {
        $ids = $this->request->array('ids');

        if ($this->roleService->roleIsBindingAdmin($ids)) {
            throw new UnprocessableEntityException('有管理员绑定此角色，请解绑后再操作');
        }

        $this->roleService->delete($ids);

        return $this->message('角色删除成功');
    }

    #[GetMapping(path: 'role/{id:\d+}')]
    #[Permission(name: '角色详情', module: '系统管理/角色管理')]
    public function detail(int $id): ResponseInterface
    {
        $role = Role::findFromCacheOrFail($id);

        return $this->success(new RoleResource($role));
    }

    #[GetMapping(path: 'role')]
    #[Get(path: 'role', summary: '角色列表', tags: ['系统管理/角色管理'])]
    #[Permission(name: '角色列表', module: '系统管理/角色管理')]
    public function index(): ResponseInterface
    {
        $pageSize = $this->request->getPageSize();
        $searchData = [
            'keyword' => $this->request->string('keyword'),
        ];

        $paginator = $this->roleService->getPaginateList($pageSize, $searchData);

        return $this->success([
            'total' => $paginator->total(),
            'list' => RoleResource::collection($paginator),
        ]);
    }

    #[GetMapping(path: 'role/selectData')]
    public function getSelectData(): ResponseInterface
    {
        $list = Role::query()
            ->select(['id', 'name'])
            ->get();

        return $this->success($list);
    }

    #[PostMapping(path: 'role/setRule')]
    #[Permission(name: '角色设置权限', module: '系统管理/角色管理', hasButton: true)]
    public function handle(SetRuleRequest $request): ResponseInterface
    {
        $ruleIds = $request->array('ruleIds');
        $roleId = $request->integer('roleId');

        Role::findFromCacheOrFail($roleId)
            ->setRule($ruleIds);

        return $this->message('权限分配成功');
    }

    #[PutMapping(path: 'role')]
    #[Permission(name: '编辑角色', module: '系统管理/角色管理', hasButton: true)]
    public function update(RoleUpdateRequest $request): ResponseInterface
    {
        $role = Role::findFromCacheOrFail($request->integer('id'));
        $dto = $request->makeDto(RoleDto::class);
        $this->roleService->update($dto,$role);

        return $this->message('角色编辑成功');
    }

    #[PatchMapping(path: 'role/status')]
    #[Permission(name: '修改角色状态', module: '系统管理/角色管理')]
    public function changeStatus(UpStatusRequest $request): ResponseInterface
    {
        $ids = $request->array('ids');
        $status = $request->integer('status');

        $this->roleService->changeStatus($ids, $status);

        $action = $status === Role::STATUS_ENABLE ? '启用' : '禁用';

        return $this->success("角色{$action}成功");
    }
}
