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
