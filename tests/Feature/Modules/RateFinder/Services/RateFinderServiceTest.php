<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\RateFinder\Services;

use Illuminate\Support\Collection;
use Modules\RateFinder\Data\FindRateDto;
use Modules\RateFinder\Gateways\ExchangeGatewayInterface;
use Modules\RateFinder\Services\RateFinderService;
use Modules\Shared\Data\OrderBook\GetOrderBookDto;
use Modules\Shared\Data\OrderBook\OrderDto;
use Modules\Shared\Data\OrderBook\OrderTypeEnum;
use Modules\Shared\Data\PairDto;
use Tests\TestCase;

class RateFinderServiceTest extends TestCase
{
    public function testFind(): void
    {
        $from = 'ETH';
        $to = 'BTC';
        $amount = 0.45;
        $findRateDto = new FindRateDto($from, $to, $amount);

        $this->mockGateway();

        /** @var RateFinderService $service */
        $service = $this->app->make(RateFinderService::class);
        $result = array_values($service->find($findRateDto)->toArray());

        $this->assertEquals(1.0, $result[0]->rate);
        $this->assertEquals('ETH', $result[0]->path->pairs->get(0)->from);
        $this->assertEquals('SOL', $result[0]->path->pairs->get(0)->to);
        $this->assertEquals(OrderTypeEnum::ASK, $result[0]->path->pairs->get(0)->direction);
        $this->assertEquals('BTC', $result[0]->path->pairs->get(1)->from);
        $this->assertEquals('SOL', $result[0]->path->pairs->get(1)->to);
        $this->assertEquals(OrderTypeEnum::BID, $result[0]->path->pairs->get(1)->direction);

        $this->assertEquals(0.1, $result[1]->rate);
        $this->assertEquals('ETH', $result[1]->path->pairs->get(0)->from);
        $this->assertEquals('BTC', $result[1]->path->pairs->get(0)->to);
        $this->assertEquals(OrderTypeEnum::ASK, $result[0]->path->pairs->get(0)->direction);

        $this->assertEquals(0.01, round($result[2]->rate, 2));
        $this->assertEquals('ETH', $result[2]->path->pairs->get(0)->from);
        $this->assertEquals('XRP', $result[2]->path->pairs->get(0)->to);
        $this->assertEquals(OrderTypeEnum::ASK, $result[0]->path->pairs->get(0)->direction);
        $this->assertEquals('XRP', $result[2]->path->pairs->get(1)->from);
        $this->assertEquals('BTC', $result[2]->path->pairs->get(1)->to);
        $this->assertEquals(OrderTypeEnum::ASK, $result[2]->path->pairs->get(1)->direction);
    }

    private function mockGateway()
    {
        $pairs = new Collection([
            new PairDto('ETH', 'XRP'),
            new PairDto('XRP', 'BTC'),
            new PairDto('ETH', 'SOL'),
            new PairDto('BTC', 'SOL'),
            new PairDto('ETH', 'BTC'),
            new PairDto('ETH', 'NEAR'),
            new PairDto('SOL', 'NEAR'),
        ]);

        $gatewayReturnETH_XRP = new Collection([
            new OrderDto(0.2, 0.3),
            new OrderDto(0.1, 0.46),
            new OrderDto(0.09, 0.54),
        ]);
        $gatewayReturnXRP_BTC = new Collection([
            new OrderDto(0.2, 0.03),
            new OrderDto(0.1, 0.046),
            new OrderDto(0.09, 0.054),
        ]);
        $gatewayReturnETH_SOL = new Collection([
            new OrderDto(0.2, 0.3),
            new OrderDto(0.1, 0.46),
            new OrderDto(0.09, 0.54),
        ]);
        $gatewayReturnBTC_SOL = new Collection([
            new OrderDto(0.2, 0.03),
            new OrderDto(0.1, 0.046),
            new OrderDto(0.09, 0.054),
        ]);
        $gatewayReturnETH_BTC = new Collection([
            new OrderDto(0.2, 0.3),
            new OrderDto(0.1, 0.46),
            new OrderDto(0.09, 0.54),
        ]);

        $gateway = $this->createMock(ExchangeGatewayInterface::class);
        $gateway->expects($this->once())->method('getMarkets')->willReturn($pairs);
        $gateway->expects($this->any())->method('getOrderBook')->willReturnCallback(function (
            GetOrderBookDto $dto
        ) use (
            $gatewayReturnETH_XRP,
            $gatewayReturnXRP_BTC,
            $gatewayReturnETH_SOL,
            $gatewayReturnBTC_SOL,
            $gatewayReturnETH_BTC,
        ) {
            if ($dto->from === 'ETH' && $dto->to === 'XRP' && $dto->orderTypeEnum === OrderTypeEnum::ASK) {
                return $gatewayReturnETH_XRP;
            }
            if ($dto->from === 'XRP' && $dto->to === 'BTC' && $dto->orderTypeEnum === OrderTypeEnum::ASK) {
                return $gatewayReturnXRP_BTC;
            }
            if ($dto->from === 'ETH' && $dto->to === 'SOL' && $dto->orderTypeEnum === OrderTypeEnum::ASK) {
                return $gatewayReturnETH_SOL;
            }
            if ($dto->from === 'BTC' && $dto->to === 'SOL' && $dto->orderTypeEnum === OrderTypeEnum::BID) {
                return $gatewayReturnBTC_SOL;
            }
            if ($dto->from === 'ETH' && $dto->to === 'BTC' && $dto->orderTypeEnum === OrderTypeEnum::ASK) {
                return $gatewayReturnETH_BTC;
            }
            return null;
        });

        $this->app->instance(ExchangeGatewayInterface::class, $gateway);
    }
}
