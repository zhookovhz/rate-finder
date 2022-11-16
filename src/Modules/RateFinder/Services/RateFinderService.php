<?php

declare(strict_types=1);

namespace Modules\RateFinder\Services;

use Modules\RateFinder\Contracts\RateFinderServiceInterface;
use Modules\RateFinder\Data\FindRateDto;
use Modules\RateFinder\Gateways\ExchangeGatewayInterface;


class RateFinderService implements RateFinderServiceInterface
{
    public function __construct(
        private readonly ExchangeGatewayInterface $exchangeGateway,
        private readonly PathFinder $pathFinder,
    ) {
    }

    public function find(FindRateDto $dto)
    {
        $pairs = $this->exchangeGateway->getMarkets();
        $paths = $this->pathFinder->findPaths($pairs, $dto);
    }
}
