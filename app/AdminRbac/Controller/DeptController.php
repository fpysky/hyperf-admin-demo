<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\CodeMsg\DeptCode;
use App\AdminRbac\Enums\DeptEnums;
use App\AdminRbac\Model\Dept\Dept;
use App\AdminRbac\Request\DeptStoreRequest;
use App\AdminRbac\Request\DeptUpdateRequest;
use App\Exception\RecordNotFoundException;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\Database\Model\ModelNotFoundException;
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
    /**
     * 部门列表
     * User: ZhouGongCe
     * Time: 2021/8/13 16:12.
     */
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

    /**
     * 部门信息详情
     * User: ZhouGongCe
     * Time: 2021/8/13 16:16.
     */
    #[GetMapping(path: '/system/backend/backendAdminDept/{id:\d+}')]
    public function edit(int $id): ResponseInterface
    {
        $dept = Dept::query()
            ->findOrFail($id);

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

    /**
     * 部门添加
     * User: ZhouGongCe
     * Time: 2021/8/13 16:12.
     * @param DeptStoreRequest $request
     * @return ResponseInterface
     */
    #[PostMapping(path: '/system/backend/backendAdminDept')]
    public function store(DeptStoreRequest $request): ResponseInterface
    {
        $name = (string) $request->input('name');

        if (Dept::exitsByName($name)) {
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

    /**
     * 部门编辑
     * User: ZhouGongCe
     * Time: 2021/8/13 16:12.
     * @param DeptUpdateRequest $request
     * @return ResponseInterface
     */
    #[PutMapping(path: '/system/backend/backendAdminDept')]
    public function update(DeptUpdateRequest $request): ResponseInterface
    {
        $name = (string) $request->input('name');
        $id = (int) $request->input('id');

        if (Dept::exitsByName($name, $id)) {
            throw new UnprocessableEntityException('部门已存在');
        }

        try {
            $dept = Dept::query()->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new RecordNotFoundException('部门不存在', DeptCode::SIX_FOUR_ZERO);
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

        $dept->update($data);

        return $this->message('部门编辑成功');
    }

    /**
     * 部门启用禁用
     * User: ZhouGongCe
     * Time: 2021/8/13 16:13.
     */
    #[PutMapping(path: '/system/backend/backendAdminDept/status')]
    public function upStatus(): ResponseInterface
    {
        $ids = $this->request->input('ids');
        $status = $this->request->input('status');

        if ($status == DeptEnums::USE) {
            $status = DeptEnums::USE;
            $msg = '部门启用成功';
        } else {
            $status = DeptEnums::DISABLE;
            $msg = '部门禁用成功';
        }

        Dept::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);

        return $this->message($msg);
    }

    /**
     * 部门删除
     * User: ZhouGongCe
     * Time: 2021/8/13 16:13.
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

    /**
     * 下拉组件所有部门.
     * @author weixiaohui
     * @email  xh_wei@juling.vip
     * @date   2021/12/30
     * @return ResponseInterface
     */
    #[GetMapping(path: '/system/backend/backendAdmin/deptTreeCombobox')]
    public function all(): ResponseInterface
    {
        $depts = Dept::query()
            ->select(['id', 'name'])
            ->where('parent_id', 0)
            ->where('status', 1)
            ->orderBy('order')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        foreach ($depts as &$dept) {
            $dept['children'] = Dept::query()
                ->select(['id', 'name'])
                ->where('parent_id', $dept['id'])
                ->where('status', 1)
                ->orderBy('order')
                ->orderBy('id', 'desc')
                ->get()
                ->toArray();
        }

        return $this->success($depts);
    }

    #[GetMapping(path: '/system/backend/backendAdminDept/deptCombobox')]
    public function deptCombobox(): ResponseInterface
    {
        $list = Dept::query()
            ->select(['id', 'name as label'])
            ->get();

        return $this->success($list);
    }
}
