<?php

declare(strict_types=1);

namespace App\Command\Rule;

use App\Model\Rule;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\HttpServer\Router\Handler;
use Hyperf\Stringable\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

#[Command]
class ApiRuleDetector extends HyperfCommand
{
    private array $routeWhiteList;

    public function __construct(protected ContainerInterface $container)
    {
        $this->routeWhiteList = \Hyperf\Config\config('notCheckRBAC');

        parent::__construct('rule:genApiRule');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('API路由探测');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws \ReflectionException
     * @throws NotFoundExceptionInterface
     */
    public function handle()
    {
        $factory = $this->container->get(DispatcherFactory::class);
        $router = $factory->getRouter('http');
        [$staticRouters] = $router->getData();

        foreach ($staticRouters as $staticRouterKey => $staticRouter) {
            foreach ($staticRouter as $routerKey => $router) {
                /** @var Handler $router */
                $route = '/' . strtolower($staticRouterKey) . $routerKey;

                if ($this->inRouteWhiteList($route)) {
                    continue;
                }

                if (! is_array($router->callback)) {
                    continue;
                }

                $controllerName = $router->callback[0];
                $methodName = $router->callback[1];
                $controllerReflection = new \ReflectionClass($controllerName);
                $method = $controllerReflection->getMethod($methodName);

                foreach ($method->getAttributes() as $mAttribute) {
                    if (! $this->attrIsRouteAnnotation($mAttribute->getName())) {
                        continue;
                    }

                    $arguments = $mAttribute->getArguments();
                    $ruleName = $arguments['summary'] ?? '';
                    $tag = $arguments['tags'][0] ?? '';

                    $parentRuleName = $this->findTagLastModule($tag);
                    $parentId = Rule::getParentMenuRuleIdByName($parentRuleName);

                    Rule::query()->updateOrCreate(['route' => $route], [
                        'parent_id' => $parentId,
                        'status' => Rule::STATUS_ENABLE,
                        'type' => Rule::TYPE_API,
                        'name' => $ruleName,
                        'route' => $route,
                    ]);
                    $this->info("创建或更新[{$route}]接口权限成功");
                }
            }
        }
        $this->info('操作完成√');
    }

    private function inRouteWhiteList(string $route): bool
    {
        return in_array($route, $this->routeWhiteList);
    }

    private function findTagLastModule(string $tagName): string
    {
        if(Str::length($tagName) === 0){
            return '';
        }

        $lastSlashOffset = strrpos($tagName, '/');
        if ($lastSlashOffset !== false) {
            return substr($tagName, strrpos($tagName, '/') + 1);
        }else{
            return '';
        }
    }

    private function attrIsRouteAnnotation(string $name): bool
    {
        return in_array($name, [
            'Hyperf\Swagger\Annotation\Get',
            'Hyperf\Swagger\Annotation\Post',
            'Hyperf\Swagger\Annotation\Delete',
            'Hyperf\Swagger\Annotation\Patch',
            'Hyperf\Swagger\Annotation\Put',
        ]);
    }
}
