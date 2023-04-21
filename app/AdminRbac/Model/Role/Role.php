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

    public static function exitsByName(string $name, int $exceptId = null): bool
    {
        $builder = self::query()->where('name', $name);

        if (! is_null($exceptId)) {
            $builder->where('id', '!=', $exceptId);
        }

        return $builder->exists();
    }
}
