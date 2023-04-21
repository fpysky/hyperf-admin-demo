<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\Enums\RoleEnums;
use App\AdminRbac\Model\Role;
use App\AdminRbac\Model\RoleRule;
use App\AdminRbac\Request\RoleStoreRequest;
use App\AdminRbac\Request\RoleUpdateRequest;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\Database\Model\ModelNotFoundException;
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
    /**
     * 角色列表
     * User: ZhouGongCe
     * Time: 2021/8/13 16:12.
     */
    #[GetMapping(path: '/system/backend/backendAdminRole')]
    public function index(): ResponseInterface
    {
        $roles = Role::query()
            ->select([
                'id', 'name', 'desc as remark',
                'created_at as createTime',
                'order as sort', 'status',
            ])
            ->orderBy('order')
            ->orderBy('id', 'desc')
            ->with([
                'roleRule' => function ($query) {
                    $query->with('rule');
                }])
            ->get()
            ->toArray();

        return $this->success(['roles' => $roles]);
    }

    /**
     * 角色添加
     * User: ZhouGongCe
     * Time: 2021/8/13 16:12.
     * @param RoleStoreRequest $request
     * @return ResponseInterface
     */
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

    /**
     * 角色编辑
     * User: ZhouGongCe
     * Time: 2021/8/13 16:12.
     * @param RoleUpdateRequest $request
     * @return ResponseInterface
     */
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

    /**
     * 为角色分配权限.
     * @return ResponseInterface
     * @throws \Exception
     * @author fengpengyuan 2023/4/4
     * @modifier fengpengyuan 2023/4/4
     */
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

    /**
     * 角色启用禁用
     * User: ZhouGongCe
     * Time: 2021/8/13 16:13.
     */
    #[PutMapping(path: '/system/backend/backendAdminRole/status')]
    public function upStatus(): ResponseInterface
    {
        $ids = $this->request->input('ids');
        $status = $this->request->input('status');

        if ($status == RoleEnums::USE) {
            $status = RoleEnums::USE;
            $msg = '角色启用成功';
        } else {
            $status = RoleEnums::DISABLE;
            $msg = '角色禁用成功';
        }

        Role::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);

        return $this->message($msg);
    }

    /**
     * 角色删除
     * User: ZhouGongCe
     * Time: 2021/8/13 16:13.
     * @throws \Exception
     */
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

    /**
     * 角色详情.
     * @param int $id
     * @return ResponseInterface
     * @author fengpengyuan 2023/4/3
     * @modifier fengpengyuan 2023/4/3
     */
    #[GetMapping(path: '/system/backend/backendAdminRole/{id:\d+}')]
    public function detail(int $id): ResponseInterface
    {
        try {
            $role = Role::query()
                ->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new UnprocessableEntityException('角色不存在');
        }

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
