<?php

declare(strict_types=1);

namespace App\Model\Merchant\Tag;

use App\Model\Merchant\Tag\Traits\MerchantTagRepository;
use App\Model\Model;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property int $sort
 * @property int $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 */
class MerchantTag extends Model
{
    use SoftDeletes;
    use MerchantTagRepository;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'merchant_tag';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['name', 'sort', 'status'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'sort' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
