<?php

declare(strict_types=1);

namespace App\Controller;

use App\Annotation\Permission;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\Dto\RuleDto;
use App\Model\RoleRule;
use App\Model\Rule;
use App\Request\RuleStoreRequest;
use App\Request\RuleUpdateRequest;
use App\Resource\Rule\RuleDetailResource;
use App\Resource\RuleResource;
use App\Resource\SelectRuleTreeResource;
use App\Service\RuleService;
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
class RuleController extends AbstractController
{
    #[Inject]
    protected RuleService $ruleService;

    #[GetMapping(path: 'rule')]
    #[Get(path: 'rule', summary: '权限列表', tags: ['系统管理/权限管理'])]
    #[Permission(name: '权限列表', module: '系统管理/权限管理')]
    public function index(): ResponseInterface
    {
        $list = $this->ruleService->getRuleTree();

        return $this->success(RuleResource::collection($list));
    }

    #[PostMapping(path: 'rule')]
    #[Permission(name: '创建权限', module: '系统管理/权限管理', hasButton: true)]
    public function store(RuleStoreRequest $request): ResponseInterface
    {
        $dto = $request->makeDto(RuleDto::class);
        $this->ruleService->create($dto);

        return $this->message('权限添加成功');
    }

    #[PutMapping(path: 'rule')]
    #[Permission(name: '编辑权限', module: '系统管理/权限管理', hasButton: true)]
    public function update(RuleUpdateRequest $request): ResponseInterface
    {
        $id = $request->integer('id');
        $rule = Rule::query()->findOrFail($id);
        $dto = $request->makeDto(RuleDto::class);
        $this->ruleService->update($rule, $dto);

        return $this->message('权限编辑成功');
    }

    #[PutMapping(path: 'system/backend/backendAdminRule/batchSortRule')]
    #[Permission(name: '批量排序权限状态', module: '系统管理/权限管理')]
    public function upOrders(): ResponseInterface
    {
        $orders = $this->request->input('orders');

        foreach ($orders as $v) {
            Rule::query()
                ->where('id', $v['id'])
                ->update(['order' => $v['sort']]);
        }

        return $this->message('权限排序成功');
    }

    #[PatchMapping(path: 'rule/status')]
    #[Permission(name: '修改权限状态', module: '系统管理/权限管理')]
    public function upStatus(): ResponseInterface
    {
        $ids = $this->request->array('ids');
        $status = $this->request->integer('status');

        Rule::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);

        if ($status == Rule::STATUS_ENABLE) {
            $msg = '权限启用成功';
        } else {
            $msg = '权限禁用成功';
        }

        return $this->message($msg);
    }

    #[DeleteMapping(path: 'rule')]
    #[Permission(name: '删除权限', module: '系统管理/权限管理', hasButton: true)]
    public function destroy(): ResponseInterface
    {
        $ids = $this->request->array('ids');
        $this->ruleService->delete($ids);

        return $this->message('权限删除成功');
    }

    #[GetMapping(path: 'rule/{id:\d+}')]
    #[Permission(name: '权限详情', module: '系统管理/权限管理')]
    public function detail(int $id): ResponseInterface
    {
        $rule = Rule::findFromCacheOrFail($id);

        return $this->success(new RuleDetailResource($rule));
    }

    #[GetMapping(path: 'rule/parentMenusTree')]
    public function parentMenusTree(): ResponseInterface
    {
        $rules = Rule::query()
            ->select(['id', 'name'])
            ->where('type', Rule::TYPE_MENU)
            ->get();

        return $this->success($rules);
    }

    #[GetMapping(path: 'rule/roleRuleTree/{roleId:\d+}')]
    public function roleRuleTree(int $roleId): ResponseInterface
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

    #[GetMapping(path: 'rule/topRule')]
    public function topRule(): ResponseInterface
    {
        $rules = Rule::query()
            ->select(['id', 'name'])
            ->where('parent_id', 0)
            ->get();

        return $this->success($rules);
    }

    #[GetMapping(path: 'rule/selectRuleTree')]
    public function selectRuleTree(): ResponseInterface
    {
        $list = Rule::query()
            ->where('parent_id', 0)
            ->with([
                'children' => function ($query) {
                    $query->with([
                        'children' => function ($query) {
                            $query->with([
                                'children' => function ($query) {
                                    $query->where('type', Rule::TYPE_MENU)
                                        ->orderBy('sort');
                                }])
                                ->where('type', Rule::TYPE_MENU)
                                ->orderBy('sort');
                        }])
                        ->orderByDesc('type')
                        ->orderBy('sort')
                        ->where('type', Rule::TYPE_MENU);
                },
            ])
            ->orderBy('sort')
            ->get();

        return $this->success(SelectRuleTreeResource::collection($list));
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
