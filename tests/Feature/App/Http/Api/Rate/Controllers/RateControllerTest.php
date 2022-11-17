<?php

declare(strict_types=1);

namespace Tests\Feature\App\Http\Api\Rate\Controllers;

use Illuminate\Support\Collection;
use Modules\RateFinder\Contracts\RateFinderServiceInterface;
use Modules\RateFinder\Data\PairWithDirectionDto;
use Modules\RateFinder\Data\PathToPairDto;
use Modules\RateFinder\Data\RateDto;
use Modules\RateFinder\Services\RateFinderService;
use Modules\Shared\Data\OrderBook\OrderTypeEnum;
use Tests\TestCase;

class RateControllerTest extends TestCase
{
    public function testGet()
    {
        $from = 'ETH';
        $to = 'BTC';
        $amount = 0.45;

        $rates = $this->createRates();

        $rateFinderService = $this->createMock(RateFinderService::class);
        $rateFinderService->expects($this->once())->method('find')->willReturn($rates);
        $this->app->instance(RateFinderServiceInterface::class, $rateFinderService);

        $response = $this->getJson(route('rate', [
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
        ]));

        $response->assertOk();
        $response->assertJson([
            'result' => [
                [
                    'path' => [
                        [
                            'from' => 'ETH',
                            'to' => 'XRP',
                            'direction' => 'ASK',
                        ],
                        [
                            'from' => 'XRP',
                            'to' => 'BTC',
                            'direction' => 'ASK',
                        ],
                    ],
                    'rate' => 0.3,
                ],
                [
                    'path' => [
                        [
                            'from' => 'ETH',
                            'to' => 'SOL',
                            'direction' => 'ASK',
                        ],
                        [
                            'from' => 'BTC',
                            'to' => 'SOL',
                            'direction' => 'BID',
                        ],
                    ],
                    'rate' => 0.2,
                ],
                [
                    'path' => [
                        [
                            'from' => 'ETH',
                            'to' => 'BTC',
                            'direction' => 'ASK',
                        ],
                    ],
                    'rate' => 0.1,
                ],
            ],
            'error' => null
        ]);
    }

    private function createRates(): Collection
    {
        return new Collection([
            new RateDto(
                new PathToPairDto(new Collection([
                    new PairWithDirectionDto('ETH', 'XRP', OrderTypeEnum::ASK),
                    new PairWithDirectionDto('XRP', 'BTC', OrderTypeEnum::ASK),
                ])),
                0.3
            ),
            new RateDto(
                new PathToPairDto(new Collection([
                    new PairWithDirectionDto('ETH', 'SOL', OrderTypeEnum::ASK),
                    new PairWithDirectionDto('BTC', 'SOL', OrderTypeEnum::BID),
                ])),
                0.2
            ),
            new RateDto(
                new PathToPairDto(new Collection([
                    new PairWithDirectionDto('ETH', 'BTC', OrderTypeEnum::ASK),
                ])),
                0.1
            ),
        ]);
    }
}
