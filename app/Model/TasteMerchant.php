<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 
 * @property int $taste_id 场景id
 * @property int $merchant_id 商家表id
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class TasteMerchant extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'taste_merchant';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'taste_id', 'merchant_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'taste_id' => 'integer', 'merchant_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
