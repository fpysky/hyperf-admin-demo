<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Rule;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Rule\Rule;
use App\Middleware\AuthMiddleware;
use Hyperf\Coroutine\Parallel;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Psr\Http\Message\ResponseInterface;

#[Controller]
#[Middlewares([AuthMiddleware::class])]
class ButtonsAction extends AbstractAction
{
    #[GetMapping(path: '/system/backend/backendAdminRule/buttons')]
    public function handle(): ResponseInterface
    {
        $buttons = Collection::make();
        $parentRules = Collection::make();

        $parallel = new Parallel(2);

        $parallel->add(function () use (&$buttons) {
            $buttons = Rule::query()
                ->where('type', Rule::TYPE_BUTTON)
                ->with([
                    'roleRule' => function (HasMany $hasMany) {
                        $hasMany->with('role')->get();
                    }])
                ->get();
        });

        $parallel->add(function () use (&$parentRules) {
            $parentIds = Rule::query()
                ->where('type', Rule::TYPE_BUTTON)
                ->pluck('parent_id')
                ->unique()
                ->toArray();
            $parentRules = Rule::query()
                ->where('type', 2)
                ->whereIn('id', $parentIds)
                ->get();
        });

        $parallel->wait();

        $data = $parentRules->map(function ($parentRule) use ($buttons) {
            $buttons0 = [];
            $buttons->each(function (Rule $button) use ($parentRule, &$buttons0) {
                if ($button->parent_id == $parentRule['id']) {
                    $buttons0[] = [
                        'id' => $button->id,
                        'name' => $button->name,
                        'status' => $button->status,
                        'icon' => $button->icon,
                        'route' => $button->route,
                        'path' => $button->path,
                        'roles' => $button->getRuleRoles(),
                    ];
                }
            });
            return [
                'id' => $parentRule['id'],
                'path' => $parentRule['path'],
                'name' => $parentRule['name'],
                'buttons' => $buttons0,
            ];
        });

        return $this->success($data);
    }
}
