<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Rule;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Rule\Rule;
use App\Middleware\AuthMiddleware;
use App\Resource\Rule\RuleResource;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Psr\Http\Message\ResponseInterface;

#[Controller]
#[Middlewares([AuthMiddleware::class])]
class DetailAction extends AbstractAction
{
    #[GetMapping(path: '/rule/{id:\d+}')]
    public function handle(int $id): ResponseInterface
    {
        $rule = Rule::findFromCacheOrFail($id);

        return $this->success(new RuleResource($rule));
    }
}
