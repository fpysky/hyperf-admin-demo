<?php

declare(strict_types=1);

namespace App\Resource;

use App\Model\Scenario;
use Hyperf\Resource\Json\JsonResource;

class ScenarioResource extends JsonResource
{
    public function toArray(): array
    {
        /** @var Scenario $this */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'coverUrl' => $this->cover_url,
            'merchantNumStr' => '1.3万', // todo::假数据
            'status' => $this->status,
        ];
    }
}
