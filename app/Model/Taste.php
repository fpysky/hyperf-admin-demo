<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id 主键id
 * @property string $title 标题
 * @property string $subtitle 副标题
 * @property string $cover_url 封面
 * @property int $status 状态 0.禁用 1.启用
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property int $deleted_at 删除时间
 */
class Taste extends Model
{
    use SoftDeletes;

    public const STATUS_DISABLE = 0;
    public const STATUS_ENABLE = 1;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'taste';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'title', 'subtitle', 'cover_url', 'status', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'integer'];
}
