<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Role;

use App\AdminRbac\Model\Origin\Role as Base;
use App\AdminRbac\Model\Role\Traits\RoleRelationship;
use Hyperf\Database\Model\SoftDeletes;

class Role extends Base
{
    use SoftDeletes;
    use RoleRelationship;

    /** 状态：启用 */
    const STATUS_ENABLE = 1;

    /** 状态：禁用 */
    const STATUS_DISABLED = 2;

    public static function exitsByName(string $name, int $exceptId = null): bool
    {
        $builder = self::query()->where('name', $name);

        if (! is_null($exceptId)) {
            $builder->where('id', '!=', $exceptId);
        }

        return $builder->exists();
    }
}
