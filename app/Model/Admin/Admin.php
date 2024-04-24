<?php

declare(strict_types=1);

namespace App\Model\Admin;

use App\Exception\RecordNotFoundException;
use App\Model\Admin\Traits\AdminRelationship;
use App\Model\Origin\Admin as Base;
use App\Model\Role\Role;
use App\Model\Role\RoleRule;
use App\Model\Rule\Rule;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property Collection $adminRole
 * @property Collection $adminDept
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
    public function setRole(array $roleIds): static
    {
        $this->clearRole();

        $insertData = array_map(function ($roleId) {
            return [
                'admin_id' => $this->id,
                'role_id' => $roleId,
            ];
        }, $roleIds);

        AdminRole::query()->insert($insertData);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function setDept(array $deptIds): static
    {
        $this->clearDept();

        $insertData = array_map(function ($deptId) {
            return [
                'admin_id' => $this->id,
                'dept_id' => $deptId,
            ];
        }, $deptIds);

        AdminDept::query()->insert($insertData);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function clearDept()
    {
        AdminDept::query()
            ->where('admin_id', $this->id)
            ->delete();
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

    public function hasDept(): bool
    {
        return $this->adminDept instanceof Collection && $this->adminDept->count();
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
            ->map(function (AdminRole $adminRole) {
                return $adminRole->role_id;
            })
            ->toArray();
    }

    public function deptIds(): array
    {
        if (! $this->hasDept()) {
            return [];
        }

        return $this->adminDept
            ->map(function (AdminDept $adminDept) {
                return $adminDept->dept_id;
            })
            ->toArray();
    }

    public function getRolesNames(): array
    {
        if (! $this->hasRole()) {
            return [];
        }

        $roleNames = [];
        $this->adminRole->each(function (AdminRole $adminRole) use (&$roleNames) {
            if ($adminRole->role instanceof Role) {
                $roleNames[] = $adminRole->role->name;
            }
        });

        return $roleNames;
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
                        ->orderBy('sort');
                },
            ])
            ->where('parent_id', 0)
            ->where('status', self::STATUS_ENABLE)
            ->whereIn('id', $adminRuleIds)
            ->where('type', Rule::TYPE_DIRECTORY)
            ->orderBy('sort')
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
