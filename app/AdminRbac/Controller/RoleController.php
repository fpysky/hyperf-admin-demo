<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Role\Role;
use App\AdminRbac\Model\Role\RoleRule;
use App\AdminRbac\Request\RoleStoreRequest;
use App\AdminRbac\Request\RoleUpdateRequest;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'role')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class RoleController extends AbstractAction
{
    #[GetMapping(path: '/system/backend/backendAdminRole')]
    public function index(): ResponseInterface
    {
        $roles = Role::query()
            ->select([
                'id', 'name', 'desc as remark',
                'created_at as createTime',
                'order as sort', 'status',
            ])
            ->with([
                'roleRule' => function ($query) {
                    $query->with('rule');
                }])
            ->orderBy('order')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        return $this->success(['roles' => $roles]);
    }

    #[PostMapping(path: '/system/backend/backendAdminRole')]
    public function store(RoleStoreRequest $request): ResponseInterface
    {
        $name = (string) $request->input('name');

        if (Role::exitsByName($name)) {
            throw new UnprocessableEntityException('角色已存在');
        }

        $data = [
            'name' => (string) $request->input('name'),
            'desc' => (string) $request->input('remark'),
            'order' => (int) $request->input('sort'),
            'status' => (int) $request->input('status'),
        ];

        Role::query()->create($data);

        return $this->message('角色添加成功');
    }

    #[PutMapping(path: '/system/backend/backendAdminRole')]
    public function update(RoleUpdateRequest $request): ResponseInterface
    {
        $name = (string) $request->input('name');
        $id = (int) $request->input('id');

        if (Role::exitsByName($name, $id)) {
            throw new UnprocessableEntityException('角色已存在');
        }

        $data = [
            'name' => (string) $request->input('name'),
            'desc' => (string) $request->input('remark'),
            'order' => (int) $request->input('sort'),
            'status' => (int) $request->input('status'),
        ];

        Role::query()
            ->where('id', $id)
            ->update($data);

        return $this->message('角色编辑成功');
    }

    #[PutMapping(path: '/system/backend/backendAdminRole/roleRule')]
    public function upRule(): ResponseInterface
    {
        $ruleIds = $this->request->input('ruleIds');
        $roleId = $this->request->input('roleId');

        RoleRule::query()
            ->where('role_id', $roleId)
            ->delete();

        $saveData = [];
        foreach ($ruleIds as $k => $v) {
            $saveData[$k]['role_id'] = $roleId;
            $saveData[$k]['rule_id'] = $v;
        }

        RoleRule::query()
            ->insert($saveData);

        return $this->message('权限分配成功');
    }

    #[PutMapping(path: '/system/backend/backendAdminRole/status')]
    public function upStatus(): ResponseInterface
    {
        $ids = (array) $this->request->input('ids');
        $status = (int) $this->request->input('status');

        Role::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);

        if ($status == Role::STATUS_ENABLE) {
            $msg = '角色启用成功';
        } else {
            $msg = '角色禁用成功';
        }

        return $this->message($msg);
    }

    #[DeleteMapping(path: '/system/backend/backendAdminRole/{ids}')]
    public function destroy(string $ids): ResponseInterface
    {
        $ids = explode(',', $ids) ?? [];
        $ids = array_filter($ids);

        // todo::这里思考一下，角色删除是不是需要查询是否有关联数据
        Role::query()
            ->whereIn('id', $ids)
            ->delete();

        return $this->message('角色删除成功');
    }

    #[GetMapping(path: '/system/backend/backendAdminRole/roleCombobox')]
    public function roleCombobox(): ResponseInterface
    {
        $list = Role::query()
            ->select(['id', 'name as label'])
            ->get();

        return $this->success($list);
    }

    #[GetMapping(path: '/system/backend/backendAdminRole/{id:\d+}')]
    public function detail(int $id): ResponseInterface
    {
        $role = Role::query()->findOrFail($id);

        $data = [
            'id' => $role->id,
            'name' => $role->name,
            'remark' => $role->desc,
            'sort' => $role->order,
            'status' => $role->status,
        ];

        return $this->success($data);
    }
}
