<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Origin;

use App\Model\Model;

/**
 * @property int $id 
 * @property string $name 职位名称
 * @property int $status 状态：0.禁用 1.启用
 * @property int $sort 排序：从小到大
 * @property string $remark 备注
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class Post extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'post';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'status', 'sort', 'remark', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'status' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
