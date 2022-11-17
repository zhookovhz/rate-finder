<?php

declare(strict_types=1);

namespace App\Http\Api\Rate\Resources;

use App\Http\Api\Shared\Resources\Resource;
use Modules\RateFinder\Data\PairWithDirectionDto;

/** @property PairWithDirectionDto $resource */
class PairWithDirectionDtoResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'from' => $this->resource->from,
            'to' => $this->resource->to,
            'direction' => $this->resource->direction->value,
        ];
    }
}
