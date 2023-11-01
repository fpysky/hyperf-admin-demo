<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Dept\Dept;
use App\AdminRbac\Request\DeptStoreRequest;
use App\AdminRbac\Request\DeptUpdateRequest;
use App\AdminRbac\Resource\Dept\DeptSelectData;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Resource\Dept\DeptResource;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class DeptController extends AbstractAction
{
    #[GetMapping(path: '/system/backend/backendAdminDept/deptCombobox')]
    public function deptCombobox(): ResponseInterface
    {
        $list = Dept::query()
            ->select(['id', 'name as label'])
            ->where('status', Dept::STATUS_ENABLE)
            ->get();

        return $this->success($list);
    }

    #[PostMapping(path: '/dept')]
    public function create(DeptStoreRequest $request): ResponseInterface
    {
        $name = $request->input('name');
        $remark = $request->input('remark');
        $parentId = (int) $request->input('parentId');
        $status = (int) $request->input('status');
        $sort = (int) $request->input('sort');

        $dept = new Dept();
        $dept->parent_id = $parentId;
        $dept->status = $status;
        $dept->sort = $sort;
        $dept->name = $name;
        $dept->remark = $remark;
        $dept->save();

        return $this->message('部门添加成功');
    }

    /**
     * @throws \Exception
     */
    #[DeleteMapping(path: '/dept/{ids}')]
    public function destroy(string $ids): ResponseInterface
    {
        $ids = explode(',', $ids) ?? [];
        $ids = array_filter($ids);

        if (! empty($ids)) {
            Dept::query()
                ->whereIn('id', $ids)
                ->delete();
        }

        return $this->message('部门删除成功');
    }

    #[GetMapping(path: '/dept')]
    public function index(): ResponseInterface
    {
        $list = Dept::query()
            ->with(['children'])
            ->where('parent_id', 0)
            ->orderBy('sort')
            ->get();

        return $this->success(DeptResource::collection($list));
    }

    #[GetMapping(path: '/dept/tree')]
    public function selectData(): ResponseInterface
    {
        $list = Dept::query()
            ->with([
                'enabledChildren' => function (HasMany $query) {
                    $query->select(['id', 'name', 'parent_id'])
                        ->orderBy('sort')
                        ->orderByDesc('id');
                },
            ])
            ->select(['id', 'name'])
            ->where('parent_id', 0)
            ->where('status', Dept::STATUS_ENABLE)
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        return $this->success(DeptSelectData::collection($list));
    }

    #[PutMapping(path: '/dept')]
    public function update(DeptUpdateRequest $request): ResponseInterface
    {
        $id = (int) $request->input('id');
        $name = $request->input('name');
        $remark = $request->input('remark');
        $parentId = (int) $request->input('parentId');
        $status = (int) $request->input('status');
        $sort = (int) $request->input('sort');

        $dept = Dept::findFromCacheOrFail($id);
        $dept->parent_id = $parentId;
        $dept->status = $status;
        $dept->sort = $sort;
        $dept->name = $name;
        $dept->remark = $remark;
        $dept->save();

        return $this->message('部门编辑成功');
    }

    #[PutMapping(path: '/dept/upStatus')]
    public function upStatus(): ResponseInterface
    {
        $ids = (array) $this->request->input('ids');
        $status = (int) $this->request->input('status');

        Dept::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);

        if ($status == Dept::STATUS_ENABLE) {
            $msg = '部门启用成功';
        } else {
            $msg = '部门禁用成功';
        }

        return $this->message($msg);
    }
}
