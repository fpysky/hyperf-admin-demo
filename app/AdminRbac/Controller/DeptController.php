<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Dept\Dept;
use App\AdminRbac\Resource\Dept\DeptSelectData;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'dept')]
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
}
