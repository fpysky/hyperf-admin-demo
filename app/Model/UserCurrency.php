<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 
 * @property int $uid 
 * @property int $points 
 * @property Carbon $created_at
 * @property string $update_at 
 */
class UserCurrency extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_currency';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'uid' => 'integer', 'points' => 'integer', 'created_at' => 'datetime'];
}
