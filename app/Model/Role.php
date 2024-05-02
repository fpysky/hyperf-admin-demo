<?php

declare(strict_types=1);

namespace App\Model;

use App\Exception\RecordNotFoundException;
use App\Model\Relationship\RoleRelationship;
use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id 主键ID
 * @property int $status 状态：0.禁用 1.启用
 * @property int $sort 排序
 * @property string $name 角色名称
 * @property string $desc 描述
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class Role extends Model
{
    use SoftDeletes;
    use RoleRelationship;

    /** 状态：启用 */
    public const STATUS_ENABLE = 1;

    /** 状态：禁用 */
    public const STATUS_DISABLED = 0;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'role';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'status', 'sort', 'name', 'desc', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'status' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public static function nameExist(string $name, ?int $exceptId = null): bool
    {
        $builder = self::query()->where('name', $name);

        if (! is_null($exceptId)) {
            $builder->where('id', '!=', $exceptId);
        }

        return $builder->exists();
    }

    /**
     * @throws \Exception
     */
    public function clearRule()
    {
        RoleRule::query()
            ->where('role_id', $this->id)
            ->delete();
    }

    /**
     * @throws \Exception
     */
    public function setRule(array $ruleIds)
    {
        $this->clearRule();

        $insertData = array_map(function ($ruleId) {
            return [
                'role_id' => $this->id,
                'rule_id' => $ruleId,
            ];
        }, $ruleIds);

        RoleRule::query()->insert($insertData);
    }

    public static function findFromCacheOrFail(int $id): self
    {
        $model = static::findFromCache($id);

        if (is_null($model)) {
            throw new RecordNotFoundException('角色不存在');
        }

        return $model;
    }

    public static function roleIsBindingAdmin(array|int $id): bool
    {
        $query = AdminRole::query();

        if (is_array($id)) {
            $query->whereIn('role_id', $id);
        } else {
            $query->where('role_id', $id);
        }

        return $query->exists();
    }
}
