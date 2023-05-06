<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Dept;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Dept\Dept;
use App\AdminRbac\Request\DeptStoreRequest;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class CreateAction extends AbstractAction
{
    #[PostMapping(path: '/dept')]
    public function handle(DeptStoreRequest $request): ResponseInterface
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
}
