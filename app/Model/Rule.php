<?php

declare(strict_types=1);

namespace App\Model;

use App\Exception\RecordNotFoundException;
use App\Model\Relationship\RuleRelationship;
use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id
 * @property int $parent_id 父级id
 * @property int $status 状态：0.禁用 1.启用
 * @property int $type 类型：1-菜单，2-目录，3-按钮，4-接口
 * @property int $sort 排序：按照从小到大排序
 * @property string $name 菜单名称
 * @property string $icon 图标
 * @property string $desc 描述
 * @property string $route api请求路由名称
 * @property string $path 菜单路由path
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class Rule extends Model
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

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'rule';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'parent_id', 'status', 'type', 'sort', 'name', 'icon', 'desc', 'route', 'path', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'parent_id' => 'integer', 'status' => 'integer', 'type' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

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

    public static function exitsByName(string $name, ?int $exceptId = null): bool
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
