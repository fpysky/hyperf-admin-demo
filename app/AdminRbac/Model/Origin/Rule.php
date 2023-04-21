<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Origin;

use App\Model\Model;

/**
 * @property int $id 
 * @property int $parent_id 父级id
 * @property int $status 状态：1-启用，2-禁用
 * @property int $type 类型：1-菜单，2-目录，3-按钮，4-接口
 * @property int $order 排序：按照从小到大排序
 * @property string $name 菜单名称
 * @property string $icon 图标
 * @property string $desc 描述
 * @property string $route api请求路由名称
 * @property string $path 菜单路由path
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class Rule extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'rule';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'parent_id', 'status', 'type', 'order', 'name', 'icon', 'desc', 'route', 'path', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'parent_id' => 'integer', 'status' => 'integer', 'type' => 'integer', 'order' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
