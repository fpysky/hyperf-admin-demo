<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Dept;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Dept\Dept;
use App\AdminRbac\Request\DeptUpdateRequest;
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
class UpdateAction extends AbstractAction
{
    #[PutMapping(path: '/dept')]
    public function handle(DeptUpdateRequest $request): ResponseInterface
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
}
