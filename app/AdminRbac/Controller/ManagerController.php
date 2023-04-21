<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\CodeMsg\AdminCode;
use App\AdminRbac\Enums\RoleEnums;
use App\AdminRbac\Enums\RuleEnums;
use App\AdminRbac\Model\Admin\Admin;
use App\AdminRbac\Model\Admin\AdminRole;
use App\AdminRbac\Model\Role\Role;
use App\AdminRbac\Model\Role\RoleRule;
use App\AdminRbac\Model\Rule\Rule;
use App\AdminRbac\Validate\ManagerValidate;
use App\AdminRbac\Validate\PasswordValidate;
use App\Exception\RecordNotFoundException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Resource\AdminMenusResource;
use App\Utils\Help;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'manager')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class ManagerController extends AbstractAction
{
    #[Inject]
    protected Help $help;

    /**
     * 管理员个人资料自编辑
     * User: ZhouGongCe
     * Time: 2021/8/13 16:17.
     * @param ManagerValidate $managerValidate
     * @return ResponseInterface
     */
    #[PutMapping(path: 'message')]
    public function upInfo(ManagerValidate $managerValidate): ResponseInterface
    {
        $adminId = $this->help->getAdminId();
        $input = $this->request->all();
        $managerValidate->check($input);

        try {
            Admin::query()->findOrFail($adminId);
        } catch (ModelNotFoundException) {
            throw new RecordNotFoundException('管理员不存在', AdminCode::SIX_ONE_ZERO);
        }

        Admin::query()
            ->where('id', $adminId)
            ->update($input);

        return $this->message('管理员编辑成功');
    }

    /**
     * 管理员密码自修改
     * User: ZhouGongCe
     * Time: 2021/8/13 16:18.
     * @param PasswordValidate $passwordValidate
     * @return ResponseInterface
     */
    #[PutMapping(path: 'password')]
    public function upPassword(PasswordValidate $passwordValidate): ResponseInterface
    {
        $adminId = $this->help->getAdminId();
        $input = $this->request->all();
        $input['id'] = $adminId;
        $passwordValidate->check($input);

        try {
            $admin = Admin::query()->findOrFail($adminId);
        } catch (ModelNotFoundException) {
            throw new RecordNotFoundException('管理员不存在', AdminCode::SIX_ONE_ZERO);
        }

        $password = $this->help
            ->encrypPassword($admin->mobile, $input['password'], $admin->getUnixCreatedAt());

        Admin::query()
            ->where('id', $input['id'])
            ->update(['password' => $password]);

        return $this->message('管理员修改密码成功');
    }

    /**
     * 管理员信息
     * User: ZhouGongCe
     * Time: 2021/8/13 16:18.
     */
    #[GetMapping(path: 'info')]
    public function info(): ResponseInterface
    {
        $adminId = $this->help->getAdminId();

        try {
            $admin = Admin::query()->findOrFail($adminId);
        } catch (ModelNotFoundException) {
            throw new RecordNotFoundException('管理员不存在', AdminCode::SIX_ONE_ZERO);
        }

        $adminRoleIds = AdminRole::query()
            ->where('admin_id', $adminId)
            ->pluck('role_id')
            ->toArray();

        $roles = Role::query()
            ->whereIn('id', $adminRoleIds)
            ->pluck('name')
            ->toArray();

        if ($admin->isSuper()) {
            array_unshift($roles, 'admin');
        }

        return $this->success([
            'admin' => $admin,
            'roles' => $roles,
        ]);
    }

    /**
     * 管理员拥有的目录菜单权限
     * User: ZhouGongCe
     * Time: 2021/8/13 16:19.
     */
    #[GetMapping(path: '/system/backend/backendAdminRule/menus')]
    public function menus(): ResponseInterface
    {
        $admin = admin();

        if ($admin->isSuper()) {
            $menus = $this->superAdminMenus();
        } else {
            $menus = $this->commonAdminMenus($admin->getId());
        }

        return $this->success(AdminMenusResource::collection($menus));
    }

    private function superAdminMenus(): Collection|array|\Hyperf\Collection\Collection
    {
        return Rule::query()
            ->with([
                'children' => function (HasMany $query) {
                    $query->where('type', RuleEnums::MENU_TYPE)
                        ->orderBy('order');
                },
            ])
            ->where('parent_id', 0)
            ->where('type', RuleEnums::DIRECTORY_TYPE)
            ->orderBy('order')
            ->get();
    }

    private function commonAdminMenus(int $adminId): Collection|array|\Hyperf\Collection\Collection
    {
        $roleIdArr = AdminRole::query()
            ->where('admin_id', $adminId)
            ->pluck('role_id')
            ->toArray();
        $roleIds = Role::query()
            ->where('status', RoleEnums::USE)
            ->whereIn('id', $roleIdArr)
            ->pluck('id')
            ->toArray();
        $ruleIds = RoleRule::query()
            ->whereIn('role_id', $roleIds)
            ->pluck('rule_id')
            ->toArray();

        return Rule::query()
            ->with(['children' => function (HasMany $query) use ($ruleIds) {
                $query->whereIn('id', $ruleIds)
                    ->where('type', RuleEnums::MENU_TYPE)
                    ->orderBy('order');
            }])
            ->whereIn('id', $ruleIds)
            ->where('parent_id', 0)
            ->where('type', RuleEnums::DIRECTORY_TYPE)
            ->orderBy('order')
            ->get();
    }
}
