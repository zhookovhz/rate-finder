<?php

declare(strict_types=1);

namespace Modules\RateFinder\Data;

use Illuminate\Support\Collection;

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
