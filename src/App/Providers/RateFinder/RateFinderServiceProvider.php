<?php

declare(strict_types=1);

namespace App\Providers\RateFinder;

use Carbon\Laravel\ServiceProvider;
use Infrastructure\Gateways\Binance\BinanceGateway;
use Modules\RateFinder\Contracts\RateFinderServiceInterface;
use Modules\RateFinder\Gateways\ExchangeGatewayInterface;
use Modules\RateFinder\Services\RateFinderService;

class RateFinderServiceProvider extends ServiceProvider
{
    public array $singletons = [
        RateFinderServiceInterface::class => RateFinderService::class,
        ExchangeGatewayInterface::class => BinanceGateway::class,
    ];
}
