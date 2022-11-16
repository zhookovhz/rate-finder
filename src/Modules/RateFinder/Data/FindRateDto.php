<?php

declare(strict_types=1);

namespace Modules\RateFinder\Data;

use Modules\Shared\Data\CryptoTickerEnum;

class FindRateDto
{
    public function __construct(
        public readonly CryptoTickerEnum $from,
        public readonly CryptoTickerEnum $to,
        public readonly float $amount
    ) {
    }
}
