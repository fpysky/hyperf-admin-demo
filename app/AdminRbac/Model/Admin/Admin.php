<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Admin;

use App\AdminRbac\Model\Admin\Traits\AdminRelationship;
use App\AdminRbac\Model\Dept\Dept;
use App\AdminRbac\Model\Origin\Admin as Base;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\SoftDeletes;
use JetBrains\PhpStorm\ArrayShape;
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
    const STATUS_ENABLE = 1;

    /** 状态：禁用 */
    const STATUS_DISABLED = 2;

    public function getId(): int
    {
        return $this->id;
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return self::query()->findOrFail((int) $key);
    }

    public static function hasSuperAdmin(array $adminIds): bool
    {
        return self::query()
            ->where('type', Admin::TYPE_SUPER)
            ->whereIn('id', $adminIds)
            ->exists();
    }

    public static function nameIsExisted(string $name, int $exceptId = 0): bool
    {
        $builder = self::query()
            ->where('name', $name);

        if (! empty($exceptId)) {
            $builder->where('id', '!=', $exceptId);
        }

        return $builder->exists();
    }

    public static function mobileIsExisted(string $mobile, int $exceptId = 0): bool
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

    public function setRole(array $roleIds)
    {
        AdminRole::query()
            ->where('admin_id', $this->id)
            ->delete();

        foreach ($roleIds as $roleId) {
            AdminRole::query()
                ->create([
                    'admin_id' => $this->id,
                    'role_id' => $roleId,
                ]);
        }
    }
}
