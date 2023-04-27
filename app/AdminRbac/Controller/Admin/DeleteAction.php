<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Admin;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\Admin;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'admin')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class DeleteAction extends AbstractAction
{
    /**
     * @throws \Exception
     */
    #[DeleteMapping(path: 'delete/{ids}')]
    public function destroy(string $ids): ResponseInterface
    {
        $ids = explode(',', $ids) ?? [];

        if (Admin::hasSuperAdmin($ids)) {
            throw new UnprocessableEntityException('不能删除超级管理员');
        }

        Admin::query()
            ->whereIn('id', $ids)
            ->delete();

        return $this->message('管理员删除成功');
    }
}
