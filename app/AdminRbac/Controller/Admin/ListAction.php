<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Admin;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\Admin;
use App\AdminRbac\Resource\AdminResource;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\Database\Model\Builder;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Stringable\Str;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller(prefix: 'admin')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class ListAction extends AbstractAction
{
    #[GetMapping(path: '')]
    public function index(): ResponseInterface
    {
        $pageSize = (int) $this->request->input('pageSize', 15);
        $keyword = (string) $this->request->input('name');

        $builder = Admin::query()
            ->with(['dept'])
            ->orderBy('id', 'desc');

        if (Str::length($keyword) !== 0) {
            $builder->where(function (Builder $builder) use ($keyword) {
                $builder->where('name', 'like', "%{$keyword}%")
                    ->orWhere('mobile', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $paginator = $builder->paginate($pageSize);

        return $this->success([
            'list' => AdminResource::collection($paginator->items()),
            'total' => $paginator->total(),
        ]);
    }
}
