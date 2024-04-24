<?php

declare(strict_types=1);

namespace App\Model\Origin;

use App\Model\Model;

/**
 * @property int $id 
 * @property int $parent_id 父级id
 * @property int $status 状态：0.禁用 1.启用
 * @property int $sort 排序
 * @property string $name 部门名称
 * @property string $remark 备注
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class Dept extends Model
{
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
}
