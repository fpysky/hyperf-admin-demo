<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\AdminLoginLog;
use App\AdminRbac\Model\AdminVisitLog;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'log')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class LogController extends AbstractAction
{
    /**
     * 登录日志
     * User: ZhouGongCe
     * Time: 2021/8/13 16:20.
     */
    #[GetMapping(path: 'loginLogs')]
    public function loginLogs(): ResponseInterface
    {
        $page = $this->request->input('page', 1);
        $pageSize = config('myconfig.pageSize.login_log_list');

        $paginator = AdminLoginLog::query()
            ->with('admin')
            ->orderBy('id', 'desc')
            ->paginate($pageSize, ['*'], 'page', $page);

        return $this->success([
            'logs' => $paginator->items(),
            'totalNum' => $paginator->total(),
            'pageSize' => $pageSize,
        ]);
    }

    /**
     * 管理员操作日志
     * User: ZhouGongCe
     * Time: 2021/8/13 16:20.
     */
    #[GetMapping(path: 'visitLogs')]
    public function visitLogs(): ResponseInterface
    {
        $page = $this->request->input('page', 1);
        $pageSize = config('myconfig.pageSize.visit_log_list');

        $paginator = AdminVisitLog::query()
            ->with('admin')
            ->orderBy('id', 'desc')
            ->paginate($pageSize, ['*'], 'page', $page);

        return $this->success([
            'logs' => $paginator->items(),
            'totalNum' => $paginator->total(),
            'pageSize' => $pageSize,
        ]);
    }
}
