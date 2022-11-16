<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\RateFinder\Services;

use Modules\RateFinder\Data\FindRateDto;
use Modules\RateFinder\Services\RateFinderService;
use Modules\Shared\Data\CryptoTickerEnum;
use Tests\TestCase;

class RateFinderServiceTest extends TestCase
{
    public function testFind(): void
    {
        /** @var RateFinderService $service */
        $service = $this->app->make(RateFinderService::class);
        $service->find(new FindRateDto(
            CryptoTickerEnum::BTC,
            CryptoTickerEnum::ETH,
            0.009
        ));
    }
}
