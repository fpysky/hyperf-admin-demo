<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\AdminRole;
use App\AdminRbac\Model\Role\Role;
use App\AdminRbac\Model\Role\RoleRule;
use App\AdminRbac\Model\Rule\Rule;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Resource\AdminMenusResource;
use App\Utils\Help;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'manager')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class ManagerController extends AbstractAction
{
    #[Inject]
    protected Help $help;

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
                    $query->where('type', Rule::TYPE_MENU)
                        ->orderBy('order');
                },
            ])
            ->where('parent_id', 0)
            ->where('type', Rule::TYPE_DIRECTORY)
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
            ->where('status', Role::STATUS_ENABLE)
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
                    ->where('type', Rule::TYPE_MENU)
                    ->orderBy('order');
            }])
            ->whereIn('id', $ruleIds)
            ->where('parent_id', 0)
            ->where('type', Rule::TYPE_DIRECTORY)
            ->orderBy('order')
            ->get();
    }
}
