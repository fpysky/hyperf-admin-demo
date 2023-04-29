<?php

namespace App\Resource\Rule;

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
        return parent::toArray();
    }
}
