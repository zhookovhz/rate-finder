<?php

declare(strict_types=1);

namespace Modules\Shared\Data;

class PairDto
{
    public function __construct(
        public readonly string $from,
        public readonly string $to,
    ) {
    }
}
