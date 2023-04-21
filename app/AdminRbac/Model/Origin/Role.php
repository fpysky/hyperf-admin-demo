<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Origin;

use App\Model\Model;

/**
 * @property int $id 主键ID
 * @property int $status 状态：1 启用 2 不启用
 * @property int $order 排序
 * @property string $name 角色名称
 * @property string $desc 描述
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class Role extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'role';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'status', 'order', 'name', 'desc', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'status' => 'integer', 'order' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
