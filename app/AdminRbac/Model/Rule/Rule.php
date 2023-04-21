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

    /** 状态：启用 */
    const STATUS_ENABLE = 1;

    /** 状态：禁用 */
    const STATUS_DISABLED = 2;

    /** 类型：目录 */
    public const TYPE_DIRECTORY = 1;

    /** 类型：菜单 */
    public const TYPE_MENU = 2;

    /** 类型：按钮 */
    public const TYPE_BUTTON = 3;

    /** 类型：接口 */
    public const TYPE_API = 4;

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
