<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 
 * @property int $uid 
 * @property int $total_spent 
 * @property int $total_orders 
 * @property Carbon $created_at
 * @property string $update_at 
 */
class UserTotal extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_total';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'uid' => 'integer', 'total_spent' => 'integer', 'total_orders' => 'integer', 'created_at' => 'datetime'];
}
