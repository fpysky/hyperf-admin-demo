<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 
 * @property int $uid 
 * @property string $openid 
 * @property string $nickname 
 * @property int $sex 
 * @property string $language 
 * @property string $city 
 * @property string $province 
 * @property string $country 
 * @property string $headimgurl 
 * @property string $unionid 
 * @property int $subscribe 
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserWechat extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_wechat';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'uid' => 'integer', 'sex' => 'integer', 'subscribe' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
