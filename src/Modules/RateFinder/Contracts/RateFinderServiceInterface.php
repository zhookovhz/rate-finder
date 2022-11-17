<?php

declare(strict_types=1);

namespace Modules\RateFinder\Contracts;

use Illuminate\Support\Collection;
use Modules\RateFinder\Data\FindRateDto;

interface RateFinderServiceInterface
{
    public function find(FindRateDto $dto): Collection;
}
