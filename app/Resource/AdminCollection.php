<?php

declare(strict_types=1);

namespace App\Resource;

use App\AdminRbac\Resource\AdminResource;
use App\Extend\Resource\Json\ResourceCollection;

class AdminCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'data' => AdminResource::collection($this->collection),
        ];
    }
}
