<?php

declare(strict_types=1);

namespace App\AdminRbac\Model;

use App\Model\Model;
use Carbon\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $number
 * @property string $company
 * @property string $address
 * @property string $email
 * @property string $mobile
 * @property string $desc
 * @property string $icp
 * @property string $slogan
 * @property string $copyright
 * @property string $domain
 * @property string $public_record
 * @property string $tel
 * @property string $company_alias
 * @property string $logo
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Website extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'website';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'name',
        'number',
        'company',
        'address',
        'email',
        'mobile',
        'desc',
        'icp',
        'slogan',
        'copyright',
        'domain',
        'public_record',
        'tel',
        'company_alias',
        'logo',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
