<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Dept;

use App\AdminRbac\Model\Dept\Traits\DeptRelationship;
use App\AdminRbac\Model\Origin\Dept as Base;
use Hyperf\Database\Model\SoftDeletes;

class Dept extends Base
{
    use SoftDeletes;
    use DeptRelationship;

    /** 状态：启用 */
    public const STATUS_ENABLE = 1;

    /** 状态：禁用 */
    public const STATUS_DISABLED = 2;

    public static function exitsByName(string $name, int $exceptId = null): bool
    {
        $builder = self::query()->where('name', $name);

        if (! is_null($exceptId)) {
            $builder->where('id', '!=', $exceptId);
        }

        return $builder->exists();
    }
}
