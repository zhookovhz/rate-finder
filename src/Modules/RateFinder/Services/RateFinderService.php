<?php

declare(strict_types=1);

namespace Modules\RateFinder\Services;

use Illuminate\Support\Collection;
use Modules\RateFinder\Contracts\RateFinderServiceInterface;
use Modules\RateFinder\Data\FindRateDto;
use Modules\RateFinder\Data\RateDto;
use Modules\RateFinder\Gateways\ExchangeGatewayInterface;

class RateFinderService implements RateFinderServiceInterface
{
    public function __construct(
        private readonly ExchangeGatewayInterface $exchangeGateway,
        private readonly PathFinder $pathFinder,
        private readonly RateCalculator $rateCalculator,
    ) {
    }

    /**
     * @param FindRateDto $dto
     * @return Collection<RateDto>
     */
    public function find(FindRateDto $dto): Collection
    {
        $pairs = $this->exchangeGateway->getMarkets();
        $paths = $this->pathFinder->findPaths($pairs, $dto);
        $rates = $this->rateCalculator->calculate($paths, $dto);

        $rates = $this->sort($rates);
        return $this->filter($rates);
    }

    /**
     * @param Collection<RateDto> $rates
     * @return Collection<RateDto>
     */
    private function sort(Collection $rates): Collection
    {
        return $rates->sort(function (RateDto $first, RateDto $second) {
            return $second->rate <=> $first->rate;
        });
    }

    /**
     * @param Collection<RateDto> $rates
     * @return Collection<RateDto>
     */
    private function filter(Collection $rates): Collection
    {
        return $rates->take(10);
    }
}
