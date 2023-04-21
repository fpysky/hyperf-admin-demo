<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Admin;

use App\AdminRbac\Enums\AdminEnums;
use App\AdminRbac\Model\Admin\Traits\AdminRelationship;
use App\AdminRbac\Model\Dept\Dept;
use App\AdminRbac\Model\Origin\Admin as Base;
use Hyperf\Database\Model\Collection;
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

    public function getId(): int
    {
        return $this->id;
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return self::query()->findOrFail((int) $key);
    }

    public static function hasSuperAdmin(array $ids): bool
    {
        return self::query()
            ->where('type', AdminEnums::superAdmin)
            ->whereIn('id', $ids)
            ->exists();
    }

    public static function existByName(string $name, int $exceptId = 0): bool
    {
        $builder = self::query()
            ->where('name', $name);

        if (! empty($exceptId)) {
            $builder->where('id', '!=', $exceptId);
        }

        return $builder->exists();
    }

    public static function existByMobile(string $mobile, int $exceptId = 0): bool
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
        return $this->type == AdminEnums::superAdmin;
    }

    public function updateLastLoginInfo(string $lastLoginIp): void
    {
        $this->last_login_ip = $lastLoginIp;
        $this->last_login_time = time();
        $this->save();
    }
}
