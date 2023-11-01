<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Role\RoleRule;
use App\AdminRbac\Model\Rule\Rule;
use App\AdminRbac\Request\RuleStoreRequest;
use App\AdminRbac\Request\RuleUpdateRequest;
use App\AdminRbac\Resource\RuleResource;
use App\Extend\CacheRule;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Resource\Rule\ButtonMenuResource;
use Hyperf\Database\Model\Relations\HasMany;
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
use Hyperf\Swagger\Annotation\Items;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Response;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class RuleController extends AbstractAction
{
    #[Inject]
    protected CacheRule $cacheRule;

    #[GetMapping(path: '/rule')]
    public function index(): ResponseInterface
    {
        $list = Rule::query()
            ->where('parent_id', 0)
            ->with([
                'children' => function ($query) {
                    $query->with('children')
                        ->orderByDesc('type')
                        ->orderBy('sort');
                },
            ])
            ->orderBy('sort')
            ->get();

        return $this->success(RuleResource::collection($list));
    }

    #[PostMapping(path: '/rule')]
    public function store(RuleStoreRequest $request): ResponseInterface
    {
        $rule = new Rule();
        $rule->parent_id = (int) $request->input('parentId');
        $rule->status = (int) $request->input('status');
        $rule->type = (int) $request->input('type');
        $rule->sort = (int) $request->input('sort');
        $rule->name = $request->input('name');
        $rule->icon = $request->input('icon');
        $rule->route = $request->input('route');
        $rule->path = $request->input('path');
        $rule->save();

        $this->cacheRule->asyncRemoveCache();

        return $this->message('权限添加成功');
    }

    #[PutMapping(path: '/rule')]
    public function update(RuleUpdateRequest $request): ResponseInterface
    {
        $id = (int) $request->input('id');

        $rule = Rule::query()->findOrFail($id);

        $rule->parent_id = (int) $request->input('parentId');
        $rule->status = (int) $request->input('status');
        $rule->type = (int) $request->input('type');
        $rule->sort = (int) $request->input('sort');
        $rule->name = $request->input('name');
        $rule->icon = $request->input('icon');
        $rule->route = $request->input('route');
        $rule->path = $request->input('path');
        $rule->save();

        $this->cacheRule->asyncRemoveCache();

        return $this->message('权限编辑成功');
    }

    #[PutMapping(path: '/system/backend/backendAdminRule/batchSortRule')]
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

    #[PatchMapping(path: '/rule/status')]
    public function upStatus(): ResponseInterface
    {
        $ids = (array) $this->request->input('ids');
        $status = (int) $this->request->input('status');

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

    /**
     * @throws \Exception
     */
    #[DeleteMapping(path: '/rule')]
    public function destroy(): ResponseInterface
    {
        $ids = (array) $this->request->input('ids', []);

        Rule::query()
            ->whereIn('id', $ids)
            ->delete();

        $this->cacheRule->asyncRemoveCache();

        return $this->message('权限删除成功');
    }

    #[GetMapping(path: '/rule/buttons')]
    #[Get(path: '/rule/buttons', summary: '按钮权限列表', tags: ['后台管理/系统管理/权限'])]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(
                property: 'data',
                description: '',
                type: 'array',
                items: new Items(
                    required: ['id', 'path', 'name'],
                    properties: [
                        new Property(property: 'id', description: 'id', type: 'integer', example: 1),
                        new Property(property: 'path', description: '菜单路由path', type: 'string', example: ''),
                        new Property(property: 'name', description: '名称', type: 'string', example: ''),
                        new Property(
                            property: 'buttons',
                            description: '按钮权限列表',
                            type: 'array',
                            items: new Items(
                                required: ['id', 'name', 'status', 'icon', 'route', 'path', 'roles'],
                                properties: [
                                    new Property(property: 'id', description: '', type: 'integer', example: ''),
                                    new Property(property: 'name', description: '名称', type: 'string', example: ''),
                                    new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: ''),
                                    new Property(property: 'icon', description: '图标', type: 'string', example: ''),
                                    new Property(property: 'route', description: '请求路由', type: 'string', example: ''),
                                    new Property(property: 'path', description: '菜单路由path', type: 'string', example: ''),
                                    new Property(property: 'roles', description: '角色列表', type: 'array', items: new Items(type: 'string', example: 'admin')),
                                ],
                                type: 'object'
                            )
                        ),
                    ]
                )
            ),
        ]
    ))]
    public function buttons(): ResponseInterface
    {
        $menuButtons = Rule::query()
            ->select(['id', 'parent_id', 'path', 'name'])
            ->with([
                'buttons' => function (HasMany $query) {
                    $query->with([
                        'roleRule' => function (HasMany $hasMany) {
                            $hasMany->with('role');
                        },
                    ]);
                },
            ])
            ->where('type', Rule::TYPE_MENU)
            ->get();

        return $this->success(ButtonMenuResource::collection($menuButtons));
    }

    #[GetMapping(path: '/rule/{id:\d+}')]
    public function detail(int $id): ResponseInterface
    {
        $rule = Rule::findFromCacheOrFail($id);

        return $this->success(new \App\Resource\Rule\RuleResource($rule));
    }

    #[GetMapping(path: '/rule/parentMenusTree')]
    public function parentMenusTree(): ResponseInterface
    {
        $rules = Rule::query()
            ->select(['id', 'name'])
            ->where('type', Rule::TYPE_MENU)
            ->get();

        return $this->success($rules);
    }

    #[GetMapping(path: '/rule/roleRuleTree/{roleId:\d+}')]
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

    #[GetMapping(path: '/rule/topRule')]
    public function handle(): ResponseInterface
    {
        $rules = Rule::query()
            ->select(['id', 'name'])
            ->where('parent_id', 0)
            ->get();

        return $this->success($rules);
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
