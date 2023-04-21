<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Rule;

use App\AdminRbac\Model\Origin\Rule as Base;
use App\AdminRbac\Model\Rule\Traits\RuleRelationship;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property Collection $roleRule
 * @property Collection $children
 * @property Rule $parentRule
 */
class Rule extends Base
{
    use SoftDeletes;
    use RuleRelationship;

    public function getRuleRoles(): array
    {
        if (! $this->roleRule->isEmpty()) {
            $rolesArr = $this->roleRule
                ->columns('role')
                ->pluck('name')
                ->toArray();
            array_unshift($rolesArr, 'admin');
        } else {
            $rolesArr = ['admin'];
        }
        return $rolesArr;
    }

    public static function exitsByName(string $name, int $exceptId = null): bool
    {
        $builder = self::query()->where('name', $name);

        if (! is_null($exceptId)) {
            $builder->where('id', '!=', $exceptId);
        }

        return $builder->exists();
    }
}
