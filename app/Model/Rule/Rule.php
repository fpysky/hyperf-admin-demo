<?php

declare(strict_types=1);

namespace App\Model\Rule;

use App\Exception\RecordNotFoundException;
use App\Model\Origin\Rule as Base;
use App\Model\Rule\Traits\RuleRelationship;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property Collection $roleRule
 * @property Collection $children
 * @property Rule $parentRule
 * @property Collection $buttons
 */
class Rule extends Base
{
    use SoftDeletes;
    use RuleRelationship;

    /** 状态：启用 */
    public const STATUS_ENABLE = 1;

    /** 状态：禁用 */
    public const STATUS_DISABLED = 0;

    /** 类型：目录 */
    public const TYPE_DIRECTORY = 1;

    /** 类型：菜单 */
    public const TYPE_MENU = 2;

    /** 类型：按钮 */
    public const TYPE_BUTTON = 3;

    /** 类型：接口 */
    public const TYPE_API = 4;

    public const TYPE_ZH = [
        self::TYPE_DIRECTORY => '目录',
        self::TYPE_MENU => '菜单',
        self::TYPE_BUTTON => '按钮',
        self::TYPE_API => '接口',
    ];

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

    public static function getSuperAdminMenus(): Collection
    {
        return self::query()
            ->with([
                'children' => function (HasMany $query) {
                    $query->where('type', Rule::TYPE_MENU)
                        ->orderBy('sort');
                },
            ])
            ->where('parent_id', 0)
            ->where('type', self::TYPE_DIRECTORY)
            ->orderBy('sort')
            ->get();
    }

    public static function findFromCacheOrFail(int $id): self
    {
        $model = static::findFromCache($id);

        if (is_null($model)) {
            throw new RecordNotFoundException('权限不存在');
        }

        return $model;
    }

    public function getTypeZh(): string
    {
        return self::TYPE_ZH[$this->type] ?? '未知类型';
    }

    public static function getParentMenuRuleIdByName(string $name): int
    {
        try {
            $parentRule = self::query()
                ->where('type', Rule::TYPE_MENU)
                ->where('name', $name)
                ->firstOrFail();
            return $parentRule->id;
        } catch (ModelNotFoundException) {
            return 0;
        }
    }
}
