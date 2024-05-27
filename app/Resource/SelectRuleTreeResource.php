<?php

declare(strict_types=1);

namespace App\Resource;

use App\Model\Rule;
use Hyperf\Resource\Json\JsonResource;

/**
 * @mixin Rule
 */
class SelectRuleTreeResource extends JsonResource
{
    public function toArray(): array
    {
        return [
            'value' => $this->id,
            'label' => $this->name,
            'children' => self::collection($this->children),
        ];
    }
}
