<?php

namespace App\Resource\Rule;

use App\AdminRbac\Model\Rule\Rule;
use Hyperf\Resource\Json\JsonResource;

class ButtonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        /** @var Rule $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'icon' => $this->icon,
            'route' => $this->route,
            'path' => $this->path,
            'roles' => $this->getRuleRoles(),
        ];
    }
}
