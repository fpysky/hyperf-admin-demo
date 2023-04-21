<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Rule;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Role\RoleRule;
use App\AdminRbac\Model\Rule\Rule;
use App\Middleware\AuthMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Psr\Http\Message\ResponseInterface;

#[Controller]
#[Middlewares([AuthMiddleware::class])]
class RuleTreeAction extends AbstractAction
{
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

        $directoryRules = $rules->where('type', Rule::TYPE_DIRECTORY)->toArray();
        $menuRules = $rules->where('type', Rule::TYPE_MENU)->toArray();
        $childrenRules = $rules->whereIn('type', [Rule::TYPE_BUTTON, Rule::TYPE_API])->toArray();
        $menuRules = $this->loadChildrenRulesToMenuRules($menuRules, $childrenRules);
        $rulesArr = $this->loadMenuRulesToDirectoryRules($directoryRules, $menuRules);

        return $this->success($rulesArr);
    }

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
