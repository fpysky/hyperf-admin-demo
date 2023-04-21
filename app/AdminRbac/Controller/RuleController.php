<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\CodeMsg\RuleCode;
use App\AdminRbac\Enums\RuleEnums;
use App\AdminRbac\Model\RoleRule;
use App\AdminRbac\Model\Rule;
use App\AdminRbac\Request\RuleStoreRequest;
use App\AdminRbac\Request\RuleUpdateRequest;
use App\AdminRbac\Resource\RuleResource;
use App\Exception\RecordNotFoundException;
use App\Extend\CacheRule;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\Utils\Parallel;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'rule')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class RuleController extends AbstractAction
{
    #[Inject]
    protected CacheRule $cacheRule;

    /**
     * 权限列表
     * User: ZhouGongCe
     * Time: 2021/8/13 16:15.
     */
    #[GetMapping(path: '/system/backend/backendAdminRule')]
    public function index(): ResponseInterface
    {
        $list = Rule::query()
            ->where('parent_id', 0)
            ->with([
                'children' => function ($query) {
                    $query->with('children')
                        ->orderBy('order');
                },
            ])
            ->orderBy('order')
            ->get();

        return $this->success(RuleResource::collection($list));
    }

    /**
     * 权限添加.
     * @param RuleStoreRequest $request
     * @return ResponseInterface
     * @author fengpengyuan 2023/4/4
     * @modifier fengpengyuan 2023/4/4
     */
    #[PostMapping(path: '/system/backend/backendAdminRule')]
    public function store(RuleStoreRequest $request): ResponseInterface
    {
        $rule = new Rule();
        $rule->parent_id = (int) $request->input('parentId');
        $rule->status = (int) $request->input('status');
        $rule->type = (int) $request->input('type');
        $rule->order = (int) $request->input('sort');
        $rule->name = $request->input('name');
        $rule->icon = $request->input('icon');
        $rule->route = $request->input('route');
        $rule->path = $request->input('path');
        $rule->save();

        $this->cacheRule->asyncRemoveCache();

        return $this->message('权限添加成功');
    }

    /**
     * 权限编辑.
     * @param RuleUpdateRequest $request
     * @return ResponseInterface
     * @author fengpengyuan 2023/4/4
     * @modifier fengpengyuan 2023/4/4
     */
    #[PutMapping(path: '/system/backend/backendAdminRule')]
    public function update(RuleUpdateRequest $request): ResponseInterface
    {
        $id = (int) $request->input('id');

        try {
            $rule = Rule::query()->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new RecordNotFoundException('权限不存在', RuleCode::SIX_THREE_ZERO);
        }

        $rule->parent_id = (int) $request->input('parentId');
        $rule->status = (int) $request->input('status');
        $rule->type = (int) $request->input('type');
        $rule->order = (int) $request->input('sort');
        $rule->name = $request->input('name');
        $rule->icon = $request->input('icon');
        $rule->route = $request->input('route');
        $rule->path = $request->input('path');
        $rule->save();

        $this->cacheRule->asyncRemoveCache();

        return $this->message('权限编辑成功');
    }

    /**
     * 权限排序
     * Created By
     * User: ZhouGongCe
     * Time: 2021/8/13 16:16.
     */
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

    /**
     * 权限停用启用.
     * @return ResponseInterface
     * @author fengpengyuan 2023/4/4
     * @modifier fengpengyuan 2023/4/4
     */
    #[PutMapping(path: '/system/backend/backendAdminRule/status')]
    public function upStatus(): ResponseInterface
    {
        $ids = $this->request->input('ids');
        $status = $this->request->input('status');

        if ($status == RuleEnums::USE) {
            $status = RuleEnums::USE;
            $msg = '权限启用成功';
        } else {
            $status = RuleEnums::DISABLE;
            $msg = '权限禁用成功';
        }

        Rule::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);

        return $this->message($msg);
    }

    /**
     * 权限删除.
     * @param string $ids
     * @return ResponseInterface
     * @throws \Exception
     * @author fengpengyuan 2023/4/4
     * @modifier fengpengyuan 2023/4/4
     */
    #[DeleteMapping(path: '/system/backend/backendAdminRule/{ids}')]
    public function destroy(string $ids): ResponseInterface
    {
        $ids = explode(',', $ids) ?? [];
        $ids = array_filter($ids);

        Rule::query()
            ->whereIn('id', $ids)
            ->delete();

        $this->cacheRule->asyncRemoveCache();

        return $this->message('权限删除成功');
    }
}
