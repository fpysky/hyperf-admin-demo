<?php

declare(strict_types=1);

namespace App\Controller;

use App\Annotation\Permission;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\Admin;
use App\Model\Dto\AdminDto;
use App\Request\AdminStoreRequest;
use App\Request\AdminUpdateRequest;
use App\Request\UpStatusRequest;
use App\Resource\AdminResource;
use App\Service\AdminService;
use App\Service\AuthService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PatchMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Items;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Patch;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\RequestBody;
use Hyperf\Swagger\Annotation\Response;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'api')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class AdminController extends AbstractController
{
    #[Inject]
    protected AdminService $adminService;

    #[Inject]
    protected AuthService $authService;

    #[PostMapping(path: 'admin')]
    #[Permission(name: '创建管理员', module: '系统管理/管理员管理', hasButton: true)]
    public function create(AdminStoreRequest $request): ResponseInterface
    {
        $dto = $request->makeDto(AdminDto::class);
        $this->adminService->create($dto);

        return $this->message('管理员添加成功');
    }

    #[DeleteMapping(path: 'admin')]
    #[Permission(name: '删除管理员', module: '系统管理/管理员管理', hasButton: true)]
    public function destroy(): ResponseInterface
    {
        $ids = $this->request->array('ids');

        if (Admin::hasSpecialAdmin($ids)) {
            throw new UnprocessableEntityException('不能删除默认管理员');
        }

        $this->adminService->delete($ids);

        return $this->message('管理员删除成功');
    }

    #[PutMapping(path: 'admin')]
    #[Permission(name: '编辑管理员', module: '系统管理/管理员管理', hasButton: true)]
    public function update(AdminUpdateRequest $request): ResponseInterface
    {
        $admin = Admin::findFromCacheOrFail($request->integer('id'));
        $dto = $request->makeDto(AdminDto::class);
        $this->adminService->update($admin, $dto);

        return $this->message('管理员编辑成功');
    }

    #[GetMapping(path: 'admin')]
    #[Permission(name: '管理员列表', module: '系统管理/管理员管理')]
    public function index(): ResponseInterface
    {
        $pageSize = $this->request->getPageSize();
        $searchOptions = [
            'keyword' => $this->request->string('keyword'),
        ];

        $paginator = $this->adminService->getAdminPaginateList($pageSize, $searchOptions);

        return $this->success([
            'list' => AdminResource::collection($paginator->items()),
            'total' => $paginator->total(),
        ]);
    }

    #[GetMapping(path: 'admin/{id:\d+}')]
    #[Permission(name: '管理员详情', module: '系统管理/管理员管理')]
    public function detail(int $id): ResponseInterface
    {
        $admin = Admin::query()
            ->with(['adminRole'])
            ->findOrFail($id);

        $data = [
            'id' => $admin->id,
            'name' => $admin->name,
            'status' => $admin->status,
            'type' => $admin->type,
            'mobile' => $admin->mobile,
            'email' => $admin->email,
            'lastLoginIp' => $admin->last_login_ip,
            'logo' => $admin->logo,
            'lastLoginTime' => $admin->last_login_time,
            'roleIds' => $admin->roleIds(),
        ];

        return $this->success($data);
    }

    #[PatchMapping(path: 'admin/status')]
    #[Permission(name: '修改管理员状态', module: '系统管理/管理员管理')]
    public function changeStatus(UpStatusRequest $request): ResponseInterface
    {
        $ids = $request->array('ids');
        $status = $request->integer('status');

        if (Admin::hasSpecialAdmin($ids)) {
            throw new UnprocessableEntityException('不能禁用特殊管理员');
        }

        $this->adminService->changeStatus($ids, $status);

        // 禁用时，强制退出
        if ($status === Admin::STATUS_DISABLED) {
            $this->authService->batchLogoutAdmin($ids);
        }

        $action = $status === Admin::STATUS_ENABLE ? '启用' : '禁用';

        return $this->success("管理员{$action}成功");
    }
}
