<?php

declare(strict_types=1);

namespace App\Model\Origin;

use App\Model\Model;

/**
 */
class DemoMigration extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'demo_migrations';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];
}
