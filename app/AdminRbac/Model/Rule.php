<?php

declare(strict_types=1);

namespace App\AdminRbac\Model;

use App\Model\Model;
use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id
 * @property int $parent_id
 * @property int $status
 * @property int $type
 * @property int $order
 * @property string $name
 * @property string $icon
 * @property string $desc
 * @property string $route
 * @property string $path
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property mixed $deleted_at
 * @property Collection $roleRule
 * @property Collection $children
 * @property Rule $parentRule
 */
class Rule extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'rule';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'parent_id',
        'status',
        'type',
        'order',
        'name',
        'icon',
        'desc',
        'route',
        'path',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'parent_id' => 'integer',
        'status' => 'integer',
        'type' => 'integer',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(Rule::class, 'parent_id', 'id');
    }

    public function roleRule(): HasMany
    {
        return $this->hasMany(RoleRule::class, 'rule_id', 'id');
    }

    public function parentRule(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'parent_id', 'id');
    }

    /**
     * @return string[]
     * @note 获取权限角色数组
     * @author fengpengyuan 2022/1/5
     * @email py_feng@juling.vip
     * @modifier fengpengyuan 2022/1/5
     */
    public function getRuleRoles(): array
    {
        if(!$this->roleRule->isEmpty()){
            $rolesArr = $this->roleRule
                ->columns('role')
                ->pluck('name')
                ->toArray();
            array_unshift($rolesArr,'admin');
        }else{
            $rolesArr = ['admin'];
        }
        return $rolesArr;
    }

    public static function exitsByName(string $name,int $exceptId = null): bool
    {
        $builder = self::query()->where('name',$name);

        if(!is_null($exceptId)){
            $builder->where('id','!=',$exceptId);
        }

        return $builder->exists();
    }
}
