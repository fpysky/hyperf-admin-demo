<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Rule;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Rule\Rule;
use App\Middleware\AuthMiddleware;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Psr\Http\Message\ResponseInterface;

#[Controller]
#[Middlewares([AuthMiddleware::class])]
class ParentMenusTreeAction extends AbstractAction
{
    #[GetMapping(path: '/rule/parentMenusTree')]
    public function handle(): ResponseInterface
    {
        $rules = Rule::query()
            ->select(['id', 'name'])
            ->where('type', Rule::TYPE_MENU)
            ->get();

        return $this->success($rules);
    }
}
