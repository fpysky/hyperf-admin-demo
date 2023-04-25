<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Dept\Dept;
use App\AdminRbac\Request\DeptStoreRequest;
use App\AdminRbac\Request\DeptUpdateRequest;
use App\AdminRbac\Resource\Dept\DeptSelectData;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'dept')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class DeptController extends AbstractAction
{
    #[GetMapping(path: '/system/backend/backendAdminDept')]
    public function index(): ResponseInterface
    {
        $list = Dept::query()
            ->with(['children'])
            ->where('parent_id', 0)
            ->orderBy('order')
            ->get()
            ->toArray();

        return $this->success($list);
    }

    #[GetMapping(path: '/system/backend/backendAdminDept/{id:\d+}')]
    public function edit(int $id): ResponseInterface
    {
        $dept = Dept::query()->findOrFail($id);

        $data = [
            'id' => $dept->id,
            'parentId' => $dept->parent_id,
            'status' => $dept->status,
            'sort' => $dept->order,
            'name' => $dept->name,
            'remark' => $dept->mark,
            'username' => $dept->username,
            'email' => $dept->email,
            'mobile' => $dept->mobile,
        ];

        return $this->success($data);
    }

    #[PostMapping(path: '/system/backend/backendAdminDept')]
    public function store(DeptStoreRequest $request): ResponseInterface
    {
        $name = (string) $request->input('name');

        if (Dept::existName($name)) {
            throw new UnprocessableEntityException('部门已存在');
        }

        $data = [
            'parent_id' => (int) $request->input('parentId'),
            'status' => (int) $request->input('status'),
            'order' => (int) $request->input('sort'),
            'name' => $name,
            'mark' => (string) $request->input('remark'),
            'username' => (string) $request->input('username'),
            'email' => (string) $request->input('email'),
            'mobile' => (string) $request->input('mobile'),
        ];

        Dept::query()->create($data);

        return $this->message('部门添加成功');
    }

    #[PutMapping(path: '/system/backend/backendAdminDept')]
    public function update(DeptUpdateRequest $request): ResponseInterface
    {
        $name = (string) $request->input('name');
        $id = (int) $request->input('id');

        if (Dept::existName($name, $id)) {
            throw new UnprocessableEntityException('部门已存在');
        }

        $dept = Dept::query()->findOrFail($id);

        $data = [
            'parent_id' => (int) $request->input('parentId'),
            'status' => (int) $request->input('status'),
            'order' => (int) $request->input('sort'),
            'name' => $name,
            'mark' => (string) $request->input('remark'),
            'username' => (string) $request->input('username'),
            'email' => (string) $request->input('email'),
            'mobile' => (string) $request->input('mobile'),
        ];

        $dept->update($data);

        return $this->message('部门编辑成功');
    }

    #[PutMapping(path: '/system/backend/backendAdminDept/status')]
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

    /**
     * @throws \Exception
     */
    #[DeleteMapping(path: '/system/backend/backendAdminDept/{ids}')]
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

    #[GetMapping(path: '/system/backend/backendAdmin/deptTreeCombobox')]
    public function deptSelectData(): ResponseInterface
    {
        $list = Dept::query()
            ->with([
                'enabledChildren' => function (HasMany $query) {
                    $query->select(['id', 'name', 'parent_id'])
                        ->orderBy('order')
                        ->orderByDesc('id');
                },
            ])
            ->select(['id', 'name'])
            ->where('parent_id', 0)
            ->where('status', Dept::STATUS_ENABLE)
            ->orderBy('order')
            ->orderByDesc('id')
            ->get();

        return $this->success(DeptSelectData::collection($list));
    }

    #[GetMapping(path: '/system/backend/backendAdminDept/deptCombobox')]
    public function deptCombobox(): ResponseInterface
    {
        $list = Dept::query()
            ->select(['id', 'name as label'])
            ->where('status', Dept::STATUS_ENABLE)
            ->get();

        return $this->success($list);
    }
}
