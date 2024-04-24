<?php

namespace App\Resource\Role;

use App\Model\Role\Role;
use Hyperf\Resource\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        /** @var Role $this */
        return [
            'id' => $this->id,
            'status' => $this->status,
            'sort' => $this->sort,
            'name' => $this->name,
            'desc' => $this->desc,
            'createdAt' => $this->getFormattedCreatedAt(),
            'updatedAt' => $this->getFormattedUpdatedAt(),
        ];
    }
}
