<?php

declare(strict_types=1);

namespace App\Extend;

use App\AdminRbac\Model\Rule;
use App\Extend\Log\Log;
use App\Extend\Redis\DefaultRedis;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Di\Annotation\Inject;

class CacheRule
{
    #[Inject]
    protected DefaultRedis $redis;

    /**
     * @throws \RedisException
     */
    public function exists(string $key): bool
    {
        $this->reloadIfNeed();

        $exists = $this->redis
            ->hExists($this->getRulesCacheKey(), $key);

        if (is_bool($exists)) {
            return $exists;
        }

        return false;
    }

    /**
     * @throws \RedisException
     */
    public function getCacheRule(string $key): string
    {
        $this->reloadIfNeed();

        $value = $this->redis
            ->hGet($this->getRulesCacheKey(), $key);

        if (is_string($value)) {
            return $value;
        }

        return '';
    }

    /**
     * @throws \RedisException
     */
    public function reloadIfNeed(): void
    {
        $cacheKey = $this->getRulesCacheKey();
        $keys = $this->redis->hKeys($cacheKey);
        $data = $this->redis->hMGet($cacheKey, $keys);

        if (empty($data)) {
            $this->loadCache();
        }
    }

    /**
     * @throws \RedisException
     */
    public function loadCache(): void
    {
        $rules = Rule::query()
            ->with([
                'parentRule' => function (BelongsTo $query) {
                    $query->select(['id', 'route', 'name', 'parent_id'])
                        ->with([
                            'parentRule' => function (BelongsTo $query) {
                                $query->select(['id', 'route', 'name', 'parent_id']);
                            },
                        ]);
                },
            ])
            ->select(['id', 'route', 'name', 'parent_id'])
            ->where('type', 4)
            ->get();

        $data = [];
        $rules->each(function (Rule $rule) use (&$data) {
            if ($rule->parentRule instanceof Rule) {
                if ($rule->parentRule->parentRule instanceof Rule) {
                    $parentParentRuleName = $rule->parentRule->parentRule->name;
                    $parentRuleName = $rule->parentRule->name;
                    $module = "$parentParentRuleName>$parentRuleName>$rule->name";
                } else {
                    $parentRuleName = $rule->parentRule->name;
                    $module = "$parentRuleName>$rule->name";
                }
            } else {
                $module = $rule->name;
            }
            $data[$rule->route] = $module;
        });

        $this->redis->hMSet($this->getRulesCacheKey(), $data);
    }

    /**
     * @throws \RedisException
     */
    public function removeCache(): void
    {
        $cacheKey = $this->getRulesCacheKey();
        $hKeys = $this->redis->hKeys($cacheKey);
        foreach ($hKeys as $key) {
            $this->redis->hDel($cacheKey, $key);
        }
    }

    public function asyncRemoveCache(): void
    {
        go(function () {
            try {
                $this->removeCache();
            } catch (\RedisException $exception) {
                Log::get()->error("api权限缓存删除失败:{$exception->getMessage()}");
            }
        });
    }

    private function getRulesCacheKey(): string
    {
        return 'system:cacheRule';
    }
}
