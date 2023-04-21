<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\Enums\AdminEnums;
use App\AdminRbac\Model\Admin\Admin;
use App\AdminRbac\Model\Admin\AdminRole;
use App\AdminRbac\Request\AdminStoreRequest;
use App\AdminRbac\Request\AdminUpdateRequest;
use App\AdminRbac\Request\ResetPasswordRequest;
use App\AdminRbac\Resource\AdminResource;
use App\Exception\UnprocessableEntityException;
use App\Extend\Auth\AuthManager;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Request\Admin\UpStatusRequest;
use App\Utils\Help;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PatchMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
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
class AdminController extends AbstractAction
{
    #[Inject]
    protected Help $help;

    #[Inject]
    protected AuthManager $auth;

    /**
     * 管理员列表.
     * @return ResponseInterface
     * @author fengpengyuan 2023/3/28
     * @modifier fengpengyuan 2023/3/28
     */
    #[GetMapping(path: '/system/backend/backendAdmin/page')]
    public function index(): ResponseInterface
    {
        $pageSize = (int) $this->request->input('pageSize', 15);
        $keyword = $this->request->input('name');

        $builder = Admin::query()
            ->with(['dept'])
            ->orderBy('id', 'desc');

        if (! empty($keyword)) {
            $builder->where(function (Builder $builder) use ($keyword) {
                $builder->where('name', 'like', "%{$keyword}%")
                    ->orWhere('mobile', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $paginator = $builder->paginate($pageSize);

        return $this->success([
            'list' => AdminResource::collection($paginator->items()),
            'total' => $paginator->total(),
        ]);
    }

    /**
     * 管理员添加
     * User: ZhouGongCe
     * Time: 2021/8/13 16:07.
     * @param AdminStoreRequest $request
     * @return ResponseInterface
     * @throws \Exception
     */
    #[PostMapping(path: '/system/backend/backendAdmin')]
    public function store(AdminStoreRequest $request): ResponseInterface
    {
        $name = (string) $request->input('name');
        $mobile = (string) $request->input('mobile');
        $password = (string) $request->input('password');

        if (Admin::existByName($name)) {
            throw new UnprocessableEntityException('姓名已存在');
        }

        if (Admin::existByMobile($mobile)) {
            throw new UnprocessableEntityException('手机号已存在，换个手机试试');
        }

        $roleIds = $request->input('roleIds');

        $storePassword = $this->help
            ->encrypPassword($mobile, $password, time());

        $data = [
            'name' => $name,
            'password' => $storePassword,
            'status' => $request->input('status'),
            'type' => 2,
            'mobile' => $request->input('mobile'),
            'email' => $request->input('email'),
            'dept_id' => $request->input('deptId'),
            'post_id' => $request->input('postId'),
        ];

        $admin = Admin::query()->create($data);

        $this->refreshRole($admin->id, $roleIds);

        return $this->message('管理员添加成功');
    }

    /**
     * 管理员编辑
     * User: ZhouGongCe
     * Time: 2021/8/13 16:07.
     * @param AdminUpdateRequest $request
     * @return ResponseInterface
     * @throws \Exception
     */
    #[PutMapping(path: '/system/backend/backendAdmin')]
    public function update(AdminUpdateRequest $request): ResponseInterface
    {
        $id = (int) $request->input('id');
        $name = (string) $request->input('name');
        $mobile = (string) $request->input('mobile');
        $password = (string) $request->input('password');
        $roleIds = (array) $request->input('roleIds');

        try {
            $admin = Admin::query()->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new UnprocessableEntityException('管理员不存在');
        }

        if (Admin::existByName($name, $admin->id)) {
            throw new UnprocessableEntityException('姓名已存在');
        }

        if ($admin->isSuper()) {
            throw new UnprocessableEntityException('超级管理员不能编辑');
        }

        $storePassword = $this->help
            ->encrypPassword($mobile, $password, time());

        $data = [
            'name' => $name,
            'password' => $storePassword,
            'status' => $request->input('status'),
            'mobile' => $mobile,
            'email' => $request->input('email'),
            'dept_id' => $request->input('deptId'),
            'post_id' => $request->input('postId'),
        ];

        $admin->update($data);

        $this->refreshRole($admin->id, $roleIds);

        return $this->message('管理员编辑成功');
    }

    /**
     * 管理员改变状态
     * @param UpStatusRequest $request
     * @return ResponseInterface
     * @throws \RedisException
     * @author fengpengyuan 2023/3/28
     * @modifier fengpengyuan 2023/3/28
     */
    #[PutMapping(path: '/system/backend/backendAdmin/status')]
    public function upStatus(UpStatusRequest $request): ResponseInterface
    {
        $ids = $request->input('ids');
        $status = $request->input('status');

        if ($status == AdminEnums::USE) {
            $status = AdminEnums::USE;
            $msg = '管理员启用成功';
        } else {
            $status = AdminEnums::DISABLE;
            $msg = '管理员禁用成功';
        }

        if (Admin::hasSuperAdmin($ids)) {
            throw new UnprocessableEntityException('不能禁用超级管理员');
        }

        Admin::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);

        // 禁用时，强制退出
        if ($status == 2) {
            foreach ($ids as $adminId) {
                $this->auth->logoutByAdminId((int) $adminId);
            }
        }

        return $this->success(['status' => $status], $msg);
    }

    #[PatchMapping(path: '/system/backend/backendAdmin/resetPassword')]
    #[Patch(path: '/system/backend/backendAdmin/resetPassword', summary: '重置管理员密码', tags: ['后台管理/系统权限管理'])]
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

        try {
            $admin = Admin::query()->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new UnprocessableEntityException('管理员不存在');
        }

        if ($admin->isSuper()) {
            throw new UnprocessableEntityException('超级管理员禁止重置密码');
        }

        $admin->password = $this->help->encrypPassword($admin->mobile, $password, $admin->getUnixCreatedAt());
        $admin->save();

        return $this->message('管理员密码重置成功');
    }

    /**
     * 管理员删除
     * User: ZhouGongCe
     * Time: 2021/8/13 16:08.
     * @param string $ids
     * @return ResponseInterface
     * @throws \Exception
     */
    #[DeleteMapping(path: '/system/backend/backendAdmin/{ids}')]
    public function destroy(string $ids): ResponseInterface
    {
        $ids = explode(',', $ids) ?? [];
        $existsSuperAdmin = Admin::query()
            ->whereIn('id', $ids)
            ->where('type', AdminEnums::superAdmin)
            ->exists();

        if ($existsSuperAdmin) {
            throw new UnprocessableEntityException('不能删除超级管理员');
        }

        Admin::query()
            ->whereIn('id', $ids)
            ->delete();

        return $this->message('管理员删除成功');
    }

    #[GetMapping(path: '/system/backend/backendAdmin/{id:\d+}')]
    public function detail(int $id): ResponseInterface
    {
        try {
            $admin = Admin::query()
                ->with(['adminRole'])
                ->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new UnprocessableEntityException('管理员不存在');
        }

        $roleIds = [];
        if ($admin->adminRole instanceof Collection && $admin->adminRole->count()) {
            $admin->adminRole->each(function (AdminRole $adminRole) use (&$roleIds) {
                $roleIds[] = $adminRole->role_id;
            });
        }

        $data = [
            'id' => $admin->id,
            'deptId' => $admin->dept_id,
            'deptIds' => [$admin->dept_id],
            'email' => $admin->email,
            'logo' => $admin->logo,
            'lastLoginIp' => $admin->last_login_ip,
            'lastLoginTime' => $admin->last_login_time,
            'mobile' => $admin->mobile,
            'name' => $admin->name,
            'postId' => $admin->post_id,
            'roleIds' => $roleIds,
            'status' => $admin->status,
            'type' => $admin->type,
        ];

        return $this->success($data);
    }

    /**
     * 设置管理员权限.
     * @param int $adminId
     * @param array $roleIds
     * @throws \Exception
     * @author fengpengyuan 2023/3/28
     * @modifier fengpengyuan 2023/3/28
     */
    private function refreshRole(int $adminId, array $roleIds)
    {
        go(function () use ($adminId, $roleIds) {
            AdminRole::query()
                ->where('admin_id', $adminId)
                ->delete();
            foreach ($roleIds as $v) {
                AdminRole::query()
                    ->create([
                        'admin_id' => $adminId,
                        'role_id' => $v,
                    ]);
            }
        });
    }
}
