<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Role\Role;
use App\AdminRbac\Request\RoleUpdateRequest;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'role')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class RoleController extends AbstractAction
{
    #[PutMapping(path: '/system/backend/backendAdminRole/status')]
    public function upStatus(): ResponseInterface
    {
        $ids = (array) $this->request->input('ids');
        $status = (int) $this->request->input('status');

        Role::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);

        if ($status == Role::STATUS_ENABLE) {
            $msg = '角色启用成功';
        } else {
            $msg = '角色禁用成功';
        }

        return $this->message($msg);
    }

    #[GetMapping(path: '/system/backend/backendAdminRole/roleCombobox')]
    public function roleCombobox(): ResponseInterface
    {
        $list = Role::query()
            ->select(['id', 'name as label'])
            ->get();

        return $this->success($list);
    }
}
