<?php

declare(strict_types=1);

namespace App\Resource\Rule;

use App\Model\Rule;
use Hyperf\Resource\Json\JsonResource;

class ButtonMenuResource extends JsonResource
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
            'path' => $this->path,
            'name' => $this->name,
            'buttons' => ButtonResource::collection($this->buttons),
        ];
    }
}
