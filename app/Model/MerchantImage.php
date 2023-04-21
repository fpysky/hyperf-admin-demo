<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 
 * @property int $merchant_id 
 * @property string $image_url 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class MerchantImage extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'merchant_image';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'merchant_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
