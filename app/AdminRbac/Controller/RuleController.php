<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Rule\Rule;
use App\AdminRbac\Request\RuleStoreRequest;
use App\AdminRbac\Request\RuleUpdateRequest;
use App\AdminRbac\Resource\RuleResource;
use App\Extend\CacheRule;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PatchMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'rule')]
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

    #[PutMapping(path: '/system/backend/backendAdminRule')]
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
}
