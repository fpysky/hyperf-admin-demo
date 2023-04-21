<?php

declare(strict_types=1);

namespace App\Resource;

use App\Model\Area\Area;
use Hyperf\Resource\Json\JsonResource;

class AreaInfo extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        /** @var Area $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'merchantNum' => 0, // todo::假数据
            'sortOrder' => $this->sort_order,
            'createdAt' => $this->getFormattedCreatedAt('Y-m-d H:i'),
        ];
    }
}
