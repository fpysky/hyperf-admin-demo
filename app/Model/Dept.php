<?php

declare(strict_types=1);

namespace App\Model;

use App\Exception\RecordNotFoundException;
use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id
 * @property int $parent_id 父级id
 * @property int $status 状态：0.禁用 1.启用
 * @property int $sort 排序
 * @property string $name 部门名称
 * @property string $remark 备注
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property null|Collection|Dept[] $children
 * @property null|Collection|Dept[] $enabledChildren
 */
class Dept extends Model
{
    use SoftDeletes;

    /** 状态：启用 */
    public const STATUS_ENABLE = 1;
    /** 状态：禁用 */
    public const STATUS_DISABLED = 0;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'dept';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'parent_id', 'status', 'sort', 'name', 'remark', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'parent_id' => 'integer', 'status' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function children(): HasMany
    {
        return $this->hasMany(Dept::class, 'parent_id', 'id');
    }

    public function enabledChildren(): HasMany
    {
        return $this->hasMany(Dept::class, 'parent_id', 'id')
            ->where('status', self::STATUS_ENABLE);
    }

    public static function existName(string $name, ?int $exceptId = null): bool
    {
        $builder = self::query()->where('name', $name);

        if (! is_null($exceptId)) {
            $builder->where('id', '!=', $exceptId);
        }

        return $builder->exists();
    }

    public static function findFromCacheOrFail(int $id): self
    {
        $model = static::findFromCache($id);

        if (is_null($model)) {
            throw new RecordNotFoundException('部门不存在');
        }

        return $model;
    }
}
