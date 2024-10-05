<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Exception\RecordNotFoundException;
use App\Exception\UnprocessableEntityException;
use App\Model\Admin;
use App\Model\AdminDept;
use App\Model\AdminRole;
use App\Model\Role;
use App\Model\RoleRule;
use App\Model\Rule;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasMany;

trait AdminRepository
{
    /**
     * @param array<int> $adminIds
     */
    public static function hasSpecialAdmin(array $adminIds): bool
    {
        if (in_array(1, $adminIds)) {
            return true;
        }

        return false;
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

    public function syncUpdateLastLoginInfo(string $lastLoginIp): void
    {
        go(function () use ($lastLoginIp) {
            $this->updateLastLoginInfo($lastLoginIp);
        });
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
    public function clearDept(): void
    {
        AdminDept::query()
            ->where('admin_id', $this->id)
            ->delete();
    }

    /**
     * @throws \Exception
     */
    public function clearRole(): void
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
     * @return array
     * @todo need to with adminRole
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
                    $query->with([
                        'children' => function (HasMany $query) use ($adminRuleIds) {
                            $query->where('status', self::STATUS_ENABLE)
                                ->whereIn('id', $adminRuleIds)
                                ->where('type', Rule::TYPE_MENU)
                                ->orderBy('sort');
                        }])
                        ->where('status', self::STATUS_ENABLE)
                        ->whereIn('id', $adminRuleIds)
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

    public function buttonPermissions(): array
    {
        $builder = Rule::query()
            ->where('status', self::STATUS_ENABLE)
            ->where('type', Rule::TYPE_BUTTON)
            ->with(['parentRule' => function (BelongsTo $query) {
                $query->with(['parentRule' => function (BelongsTo $query) {
                    $query->with(['parentRule' => function (BelongsTo $query) {
                        $query->with(['parentRule' => function (BelongsTo $query) {
                            $query->with(['parentRule']);
                        }]);
                    }]);
                }]);
            }]);

        $adminRuleIds = [];
        if (! $this->isSuper()) {
            $adminRuleIds = $this->ruleIds();
            $builder->whereIn('id', $adminRuleIds);
        }

        $list = $builder->get();
        $ruleArr = [];
        $list->each(function (Rule $rule) use (&$ruleArr, $adminRuleIds) {
            if ($this->isSuper()) {
                $ruleArr[] = $rule->getNamePath();
            } else {
                if (in_array($rule->id, $adminRuleIds)) {
                    $ruleArr[] = $rule->getNamePath();
                }
            }
        });

        return $ruleArr;
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
