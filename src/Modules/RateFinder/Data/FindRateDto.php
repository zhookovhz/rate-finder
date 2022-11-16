<?php

declare(strict_types=1);

namespace Modules\RateFinder\Data;


class FindRateDto
{
    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly float $amount
    ) {
    }
}
