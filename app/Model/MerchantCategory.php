<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 
 * @property int $parent_id 
 * @property string $id_path 
 * @property string $path 
 * @property string $category_name 
 * @property string $icon_url 
 * @property int $sort_order 
 * @property string $sort_path 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class MerchantCategory extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'merchant_category';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'parent_id' => 'integer', 'sort_order' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
