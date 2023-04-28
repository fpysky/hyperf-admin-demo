<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Admin;

use App\AdminRbac\Model\Admin\Traits\AdminRelationship;
use App\AdminRbac\Model\Dept\Dept;
use App\AdminRbac\Model\Origin\Admin as Base;
use App\AdminRbac\Model\Role\RoleRule;
use App\AdminRbac\Model\Rule\Rule;
use App\Exception\RecordNotFoundException;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property Collection $adminRole
 * @property Dept $dept
 */
class Admin extends Base implements Authenticatable
{
    use AdminRelationship;
    use SoftDeletes;

    /** 类型：超级管理员 */
    public const TYPE_SUPER = 1;

    /** 类型：普通管理员 */
    public const TYPE_NORMAL = 2;

    /** 状态：启用 */
    public const STATUS_ENABLE = 1;

    /** 状态：禁用 */
    public const STATUS_DISABLED = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return self::query()->findOrFail((int) $key);
    }

    /**
     * @param array<int> $adminIds
     */
    public static function hasSuperAdmin(array $adminIds): bool
    {
        return self::query()
            ->where('type', Admin::TYPE_SUPER)
            ->whereIn('id', $adminIds)
            ->exists();
    }

    public static function existName(string $name, int $exceptId = 0): bool
    {
        $builder = self::query()
            ->where('name', $name);

        if (! empty($exceptId)) {
            $builder->where('id', '!=', $exceptId);
        }

        return $builder->exists();
    }

    public static function existMobile(string $mobile, int $exceptId = 0): bool
    {
        $builder = Admin::query()
            ->where('mobile', $mobile);

        if (! empty($exceptId)) {
            $builder->where('id', '!=', $exceptId);
        }

        return $builder->exists();
    }

    public function isSuper(): bool
    {
        return $this->type == self::TYPE_SUPER;
    }

    public function isDisabled(): bool
    {
        return $this->status === self::STATUS_DISABLED;
    }

    public function updateLastLoginInfo(string $lastLoginIp): void
    {
        $this->last_login_ip = $lastLoginIp;
        $this->last_login_time = time();
        $this->save();
    }

    /**
     * @param array<int> $roleIds
     * @throws \Exception
     */
    public function setRole(array $roleIds)
    {
        $this->clearRole();

        $insertData = array_map(function ($roleId) {
            return [
                'admin_id' => $this->id,
                'role_id' => $roleId,
            ];
        }, $roleIds);

        AdminRole::query()->insert($insertData);
    }

    /**
     * @throws \Exception
     */
    public function clearRole()
    {
        AdminRole::query()
            ->where('admin_id', $this->id)
            ->delete();
    }

    public function hasRole(): bool
    {
        return $this->adminRole instanceof Collection && $this->adminRole->count();
    }

    /**
     * @todo need to with adminRole
     * @return array
     */
    public function roleIds(): array
    {
        if (! $this->hasRole()) {
            return [];
        }

        return $this->adminRole
            ->map(function (AdminRole $adminRole) use (&$roleIds) {
                return $adminRole->role_id;
            })
            ->toArray();
    }

    public function ruleIds(): array
    {
        return RoleRule::query()
            ->whereIn('role_id', $this->roleIds())
            ->pluck('rule_id')
            ->toArray();
    }

    public function menus(): Collection
    {
        $adminRuleIds = $this->ruleIds();

        return Rule::query()
            ->with([
                'children' => function (HasMany $query) use ($adminRuleIds) {
                    $query->whereIn('id', $adminRuleIds)
                        ->where('type', Rule::TYPE_MENU)
                        ->orderBy('order');
                },
            ])
            ->where('parent_id', 0)
            ->where('status', self::STATUS_ENABLE)
            ->whereIn('id', $adminRuleIds)
            ->where('type', Rule::TYPE_DIRECTORY)
            ->orderBy('order')
            ->get();
    }

    public static function findFromCacheOrFail(int $id): self
    {
        $model = static::findFromCache($id);

        if (is_null($model)) {
            throw new RecordNotFoundException('管理员不存在');
        }

        return $model;
    }
}
