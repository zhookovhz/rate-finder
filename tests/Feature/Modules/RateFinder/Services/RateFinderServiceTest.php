<?php
//
//declare(strict_types=1);
//
//namespace Tests\Feature\Modules\RateFinder\Services;
//
//use Illuminate\Support\Collection;
//use Modules\RateFinder\Data\FindRateDto;
//use Modules\RateFinder\Data\PairWithDirectionDto;
//use Modules\RateFinder\Data\PathToPairDto;
//use Modules\RateFinder\Gateways\ExchangeGatewayInterface;
//use Modules\RateFinder\Services\RateFinderService;
//use Modules\Shared\Data\OrderBook\GetOrderBookDto;
//use Modules\Shared\Data\OrderBook\OrderDto;
//use Modules\Shared\Data\OrderBook\OrderTypeEnum;
//use Modules\Shared\Data\PairDto;
//use Tests\TestCase;
//
//class RateFinderServiceTest extends TestCase
//{
//    public function testFind(): void
//    {
//        $from = 'ETH';
//        $to = 'BTC';
//        $amount = 0.45;
//        $findRateDto = new FindRateDto($from, $to, $amount);
//
//        $this->mockGateway();
//
//        /** @var RateFinderService $service */
//        $service = $this->app->make(RateFinderService::class);
//        $result = $service->find($findRateDto);
//
//        123;
//    }
//
//    private function mockGateway()
//    {
//        $pairs = [
//            new PairDto('ETH', 'XRP'),
//            new PairDto('XRP', 'BTC'),
//            new PairDto('ETH', 'SOL'),
//            new PairDto('BTC', 'SOL'),
//            new PairDto('ETH', 'NEAR'),
//            new PairDto('BTC', 'APT'),
//            new PairDto('BTC', 'ETH'),
//        ];
//        $pairs = new Collection($pairs);
//
//
//        $gatewayReturnETH_XRP = new Collection([
//            new OrderDto(0.2, 0.3),
//            new OrderDto(0.1, 0.46),
//            new OrderDto(0.09, 0.54),
//        ]);
//        $gatewayReturnXRP_BTC = new Collection([
//            new OrderDto(0.2, 0.03),
//            new OrderDto(0.1, 0.046),
//            new OrderDto(0.09, 0.054),
//        ]);
//        $gatewayReturnETH_SOL = new Collection([
//            new OrderDto(0.2, 0.3),
//            new OrderDto(0.1, 0.46),
//            new OrderDto(0.09, 0.54),
//        ]);
//        $gatewayReturnBTC_SOL = new Collection([
//            new OrderDto(0.2, 0.03),
//            new OrderDto(0.1, 0.046),
//            new OrderDto(0.09, 0.054),
//        ]);
//        $gatewayReturnBTC_ETH = new Collection([
//            new OrderDto(0.2, 0.3),
//            new OrderDto(0.1, 0.46),
//            new OrderDto(0.09, 0.54),
//        ]);
//
//        $gateway = $this->createMock(ExchangeGatewayInterface::class);
//        $gateway->expects($this->once())->method('getMarkets')->willReturn($pairs);
//        $gateway->expects($this->any())->method('getOrderBook')->willReturnCallback(function (
//            GetOrderBookDto $dto
//        ) use (
//            $gatewayReturnETH_XRP,
//            $gatewayReturnXRP_BTC,
//            $gatewayReturnETH_SOL,
//            $gatewayReturnBTC_SOL,
//            $gatewayReturnBTC_ETH,
//        ) {
//            if ($dto->from === 'ETH' && $dto->to === 'XRP' && $dto->orderTypeEnum === OrderTypeEnum::ASK) {
//                return $gatewayReturnETH_XRP;
//            }
//            if ($dto->from === 'XRP' && $dto->to === 'BTC' && $dto->orderTypeEnum === OrderTypeEnum::ASK) {
//                return $gatewayReturnXRP_BTC;
//            }
//            if ($dto->from === 'ETH' && $dto->to === 'SOL' && $dto->orderTypeEnum === OrderTypeEnum::ASK) {
//                return $gatewayReturnETH_SOL;
//            }
//            if ($dto->from === 'BTC' && $dto->to === 'SOL' && $dto->orderTypeEnum === OrderTypeEnum::BID) {
//                return $gatewayReturnBTC_SOL;
//            }
//            if ($dto->from === 'BTC' && $dto->to === 'ETH' && $dto->orderTypeEnum === OrderTypeEnum::ASK) {
//                return $gatewayReturnBTC_ETH;
//            }
//            return null;
//        });
//    }
//}
