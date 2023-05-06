<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Dept;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Dept\Dept;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class UpStatusAction extends AbstractAction
{
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
