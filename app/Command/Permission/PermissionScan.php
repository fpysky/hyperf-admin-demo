<?php

declare(strict_types=1);

namespace App\Command\Permission;

use App\Annotation\Permission;
use App\Model\Rule;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\HttpServer\Router\Handler;
use Hyperf\Stringable\Str;
use Psr\Container\ContainerInterface;

#[Command]
class PermissionScan extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('permission:scan');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('权限扫描器');
    }

    public function handle()
    {
        $factory = $this->container->get(DispatcherFactory::class);
        $router = $factory->getRouter('http');
        [$staticRouters] = $router->getData();

        foreach ($staticRouters as $staticRouterKey => $staticRouter) {
            foreach ($staticRouter as $routerKey => $router) {
                /** @var Handler $router */
                $route = '/' . strtolower($staticRouterKey) . $routerKey;

                if (! is_array($router->callback)) {
                    continue;
                }

                $controllerName = $router->callback[0];
                $methodName = $router->callback[1];
                $controllerReflection = new \ReflectionClass($controllerName);
                $method = $controllerReflection->getMethod($methodName);

                if ($this->attrHasPermissionAnnotation($method->getAttributes())) {
                    foreach ($method->getAttributes() as $mAttribute) {
                        if (! $this->isPermissionAnnotation($mAttribute->getName())) {
                            continue;
                        }

                        $arguments = $mAttribute->getArguments();
                        $permissionName = $arguments['name'] ?? '';
                        $moduleStr = $arguments['module'] ?? '';
                        $parentRuleName = $this->findLastModule($moduleStr);
                        $parentId = Rule::getParentMenuRuleIdByName($parentRuleName);

                        Rule::query()->updateOrCreate(['route' => $route], [
                            'parent_id' => $parentId,
                            'status' => Rule::STATUS_ENABLE,
                            'type' => Rule::TYPE_API,
                            'name' => $permissionName,
                            'route' => $route,
                        ]);
                        $this->info("创建或更新[{$route}]接口权限成功");
                    }
                }
            }
        }
        $this->info('操作完成√');
    }

    private function findLastModule(string $moduleStr): string
    {
        if (Str::length($moduleStr) === 0) {
            return '';
        }

        $lastSlashOffset = strrpos($moduleStr, '/');
        if ($lastSlashOffset !== false) {
            return substr($moduleStr, strrpos($moduleStr, '/') + 1);
        }
        return '';
    }

    private function attrHasPermissionAnnotation(array $attributes): bool
    {
        foreach ($attributes as $attribute) {
            if ($this->isPermissionAnnotation($attribute->getName())) {
                return true;
            }
        }

        return false;
    }

    private function isPermissionAnnotation(string $name): bool
    {
        return $name === Permission::class;
    }
}
