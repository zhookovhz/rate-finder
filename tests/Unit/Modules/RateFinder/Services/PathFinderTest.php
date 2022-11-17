<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\RateFinder\Services;

use Illuminate\Support\Collection;
use Modules\RateFinder\Data\FindRateDto;
use Modules\RateFinder\Data\PathToPairDto;
use Modules\RateFinder\Services\PathFinder;
use Modules\Shared\Data\OrderBook\OrderTypeEnum;
use Modules\Shared\Data\PairDto;
use Tests\TestCase;

class PathFinderTest extends TestCase
{
    public function testFindPaths(): void
    {
        $findDto = new FindRateDto('BTC', 'ETH', 0.093);
        $pairsArray = [
            ['from' => 'ETH', 'to' => 'BTC'],
            ['from' => 'BTC', 'to' => 'XRP'],
            ['from' => 'ETH', 'to' => 'XRP'],
            ['from' => 'BTC', 'to' => 'SOL'],
            ['from' => 'SOL', 'to' => 'ETH'],
        ];
        $pairsDtoArray = [];
        foreach ($pairsArray as $pair) {
            $pairsDtoArray[] = new PairDto($pair['from'], $pair['to']);
        }
        $pairsDtoCollection = new Collection($pairsDtoArray);

        $finder = new PathFinder();
        $result = $finder->findPaths($pairsDtoCollection, $findDto);

        /** @var PathToPairDto $firstPath */
        $firstPath = $result->pop();
        /** @var PathToPairDto $secondPath */
        $secondPath = $result->pop();
        /** @var PathToPairDto $thirdPath */
        $thirdPath = $result->pop();

        $this->assertEquals('BTC', $firstPath->pairs->get(0)->from);
        $this->assertEquals('SOL', $firstPath->pairs->get(0)->to);
        $this->assertEquals(OrderTypeEnum::ASK, $firstPath->pairs->get(0)->direction);

        $this->assertEquals('SOL', $firstPath->pairs->get(1)->from);
        $this->assertEquals('ETH', $firstPath->pairs->get(1)->to);
        $this->assertEquals(OrderTypeEnum::ASK, $firstPath->pairs->get(1)->direction);

        $this->assertEquals('BTC', $secondPath->pairs->get(0)->from);
        $this->assertEquals('XRP', $secondPath->pairs->get(0)->to);
        $this->assertEquals(OrderTypeEnum::ASK, $secondPath->pairs->get(0)->direction);

        $this->assertEquals('ETH', $secondPath->pairs->get(1)->from);
        $this->assertEquals('XRP', $secondPath->pairs->get(1)->to);
        $this->assertEquals(OrderTypeEnum::BID, $secondPath->pairs->get(1)->direction);

        $this->assertEquals('ETH', $thirdPath->pairs->get(0)->from);
        $this->assertEquals('BTC', $thirdPath->pairs->get(0)->to);
        $this->assertEquals(OrderTypeEnum::BID, $thirdPath->pairs->get(0)->direction);
    }
}
