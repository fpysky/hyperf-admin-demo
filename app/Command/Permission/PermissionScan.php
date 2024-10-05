<?php

declare(strict_types=1);

namespace App\Command\Permission;

use App\Annotation\Permission;
use App\Model\Rule;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PatchMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
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
        $collector = AnnotationCollector::list();
        foreach ($collector as $metadata) {
            if (isset($metadata['_c'][Controller::class], $metadata['_m'])) {
                foreach ($metadata['_m'] as $value) {
                    if (isset($value[Permission::class])) {
                        /** @var Controller $controller */
                        $controller = $metadata['_c'][Controller::class];
                        /** @var Permission $permission */
                        $permission = $value[Permission::class];

                        $parentRuleName = $this->findLastModule($permission->module);
                        $parentId = Rule::getParentMenuRuleIdByName($parentRuleName);
                        $route = $this->getRequestPath($controller->prefix, $value);

                        Rule::query()->updateOrCreate(['route' => $route], [
                            'parent_id' => $parentId,
                            'status' => Rule::STATUS_ENABLE,
                            'type' => Rule::TYPE_API,
                            'name' => $permission->name,
                            'route' => $route,
                        ]);
                        $this->info("创建或更新[{$route}]接口权限成功");
                    }
                }
            }
        }
        $this->info('操作完成√');
    }

    public function getRequestPath(string $prefix, array $methodMetadata): string
    {
        if (Str::length($prefix)) {
            if (Str::substr($prefix, 0, 1) === '/') {
                $prefix = Str::substr($prefix, 1);
            }
            if (Str::substr($prefix, -1, 1) !== '/') {
                $prefix .= '/';
            }
        }

        return match (true) {
            isset($methodMetadata[GetMapping::class]) => "/get/$prefix{$methodMetadata[GetMapping::class]->path}",
            isset($methodMetadata[PostMapping::class]) => "/post/$prefix{$methodMetadata[PostMapping::class]->path}",
            isset($methodMetadata[PutMapping::class]) => "/put/$prefix{$methodMetadata[PutMapping::class]->path}",
            isset($methodMetadata[PatchMapping::class]) => "/patch/$prefix{$methodMetadata[PatchMapping::class]->path}",
            isset($methodMetadata[DeleteMapping::class]) => "/delete/$prefix{$methodMetadata[DeleteMapping::class]->path}",
            default => '',
        };
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
}
