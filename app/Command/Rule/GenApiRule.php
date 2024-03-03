<?php

declare(strict_types=1);

namespace App\Command\Rule;

use App\AdminRbac\Model\Rule\Rule;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\HttpServer\Router\Handler;
use Hyperf\Stringable\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

#[Command]
class GenApiRule extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('rule:genApiRule');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('genApiRule');
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
                // todo::去掉通用路由

                $ruleExists = Rule::query()
                    ->where('route', $route)
                    ->exists();

                if (! $ruleExists) {
                    // 如何猜测一个接口属于哪个模块？
                    if (is_array($router->callback)) {
                        $controllerName = $router->callback[0];
                        $methodName = $router->callback[1];
                        $controllerReflection = new \ReflectionClass($controllerName);
                        $method = $controllerReflection->getMethod($methodName);
                        foreach ($method->getAttributes() as $mAttribute) {
                            $name = $mAttribute->getName();
                            if (in_array($name, [
                                'Hyperf\Swagger\Annotation\Get',
                                'Hyperf\Swagger\Annotation\Post',
                                'Hyperf\Swagger\Annotation\Delete',
                                'Hyperf\Swagger\Annotation\Patch',
                                'Hyperf\Swagger\Annotation\Put',
                            ])) {
                                $arguments = $mAttribute->getArguments();
                                $ruleName = $arguments['summary'] ?? '';
                                $tag = $arguments['tags'][0] ?? '';
                                $parentId = 0;

                                if (Str::length($tag) !== 0) {
                                    $offset = strrpos($tag, '/');
                                    if ($offset !== false) {
                                        $parentRuleName = substr($tag, strrpos($tag, '/') + 1);
                                        try {
                                            $parentRule = Rule::query()->where('type', Rule::TYPE_MENU)
                                                ->where('name', $parentRuleName)
                                                ->firstOrFail();
                                            $parentId = $parentRule->id;
                                        } catch (ModelNotFoundException) {
                                        }
                                    }
                                }

                                Rule::query()->create([
                                    'parent_id' => $parentId,
                                    'status' => Rule::STATUS_ENABLE,
                                    'type' => Rule::TYPE_API,
                                    'name' => $ruleName,
                                    'route' => $route,
                                    'path' => '',
                                ]);
                            }
                        }
                    }

                    $this->info("创建[{$route}]接口权限成功");
                }
            }
        }
        $this->info('操作完成√');
    }
}
