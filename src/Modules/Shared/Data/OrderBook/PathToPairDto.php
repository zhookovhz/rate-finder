<?php

declare(strict_types=1);

namespace Modules\Shared\Data\OrderBook;

use Illuminate\Support\Collection;
use Modules\Shared\Data\PairWithDirectionDto;

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
