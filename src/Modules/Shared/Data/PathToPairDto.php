<?php

declare(strict_types=1);

namespace Modules\Shared\Data;

use Illuminate\Support\Collection;
use Modules\Shared\Data\OrderBook\PairWithDirectionDto;

class PathToPairDto
{
    /**
     * @param Collection<PairWithDirectionDto> $pairs
     */
    public function __construct(
        public readonly Collection $pairs,
    ) {
    }
}
