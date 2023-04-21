<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Rule;

use App\Actions\AbstractAction;
use App\AdminRbac\Enums\RuleEnums;
use App\AdminRbac\Model\RoleRule;
use App\AdminRbac\Model\Rule;
use App\Middleware\AuthMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Psr\Http\Message\ResponseInterface;

#[Controller]
#[Middlewares([AuthMiddleware::class])]
class RuleTreeAction extends AbstractAction
{
    /**
     * 角色权限树.
     * @param int $roleId
     * @return ResponseInterface
     * @author fengpengyuan 2023/3/28
     * @modifier fengpengyuan 2023/3/28
     */
    #[GetMapping(path: '/system/backend/backendAdminRule/ruleTree/{roleId:\d+}')]
    public function handle(int $roleId): ResponseInterface
    {
        $ruleIds = RoleRule::query()
            ->where('role_id', $roleId)
            ->pluck('rule_id')
            ->toArray();

        $rules = Rule::query()
            ->select(['id', 'name', 'type', 'parent_id'])
            ->whereIn('id', $ruleIds)
            ->orderBy('type')
            ->get();

        $directoryRules = $rules->where('type', RuleEnums::DIRECTORY_TYPE)->toArray();
        $menuRules = $rules->where('type', RuleEnums::MENU_TYPE)->toArray();
        $childrenRules = $rules->whereIn('type', [RuleEnums::BUTTON_TYPE, RuleEnums::API_TYPE])->toArray();
        $menuRules = $this->loadChildrenRulesToMenuRules($menuRules, $childrenRules);
        $rulesArr = $this->loadMenuRulesToDirectoryRules($directoryRules, $menuRules);

        return $this->success($rulesArr);
    }

    /**
     * 装载子权限到菜单权限.
     * @param array $menuRules
     * @param array $childrenRules
     * @return array
     * @author fengpengyuan 2023/3/28
     * @modifier fengpengyuan 2023/3/28
     */
    private function loadChildrenRulesToMenuRules(array $menuRules, array $childrenRules): array
    {
        foreach ($menuRules as $menuRuleKey => $menuRule) {
            $menuRules[$menuRuleKey]['children'] = [];
            foreach ($childrenRules as $childRule) {
                if ($childRule['parent_id'] == $menuRule['id']) {
                    $menuRules[$menuRuleKey]['children'][] = $childRule;
                }
            }
        }
        return $menuRules;
    }

    /**
     * @param array $directoryRules
     * @param array $menuRules
     * @return array
     * @note 装载菜单权限到目录权限
     * @author fengpengyuan 2022/1/6
     * @email py_feng@juling.vip
     * @modifier fengpengyuan 2022/1/6
     */
    private function loadMenuRulesToDirectoryRules(array $directoryRules, array $menuRules): array
    {
        foreach ($directoryRules as $directoryRuleKey => $directoryRule) {
            $directoryRules[$directoryRuleKey]['children'] = [];
            foreach ($menuRules as $menuRule) {
                if ($menuRule['parent_id'] == $directoryRule['id']) {
                    $directoryRules[$directoryRuleKey]['children'][] = $menuRule;
                }
            }
        }
        return $directoryRules;
    }
}
