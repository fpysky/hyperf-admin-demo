<?php

declare(strict_types=1);

namespace App\AdminRbac\Resource;

use App\AdminRbac\Model\Rule;
use Hyperf\Resource\Json\JsonResource;

class RuleResource extends JsonResource
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
            'parentId' => $this->parent_id,
            'status' => $this->status,
            'type' => $this->type,
            'sort' => $this->order,
            'name' => $this->name,
            'icon' => $this->icon,
            'route' => $this->route,
            'path' => $this->path,
            'children' => self::collection($this->children),
        ];
    }
}
