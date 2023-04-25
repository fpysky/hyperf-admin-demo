<?php

declare(strict_types=1);

namespace App\Extend\Resource\Response;

use Hyperf\Paginator\LengthAwarePaginator;
use Hyperf\Resource\Response\PaginatedResponse as Base;

class PaginatedResponse extends Base
{
    public function toArrResponse(): array
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = $this->resource->resource;
        $resolveData = $this->resource->resolve();

        return [
            'list' => $resolveData['data'],
            'total' => $paginator->total(),
        ];
    }
}
