<?php

declare(strict_types=1);

namespace Modules\RateFinder\Services;

use Illuminate\Support\Collection;
use Modules\RateFinder\Data\FindRateDto;
use Modules\Shared\Data\OrderBook\OrderTypeEnum;
use Modules\Shared\Data\OrderBook\PairWithDirectionDto;
use Modules\Shared\Data\PairDto;
use Modules\Shared\Data\PathToPairDto;

class PathFinder
{
    /**
     * @param Collection<PairDto> $pairs
     * @param FindRateDto $dto
     * @return Collection<PathToPairDto>
     */
    public function findPaths(Collection $pairs, FindRateDto $dto): Collection
    {
        $direct = null;
        $reverse = null;
        $fromDirect = [];
        $toDirect = [];
        $fromReverse = [];
        $toReverse = [];

        foreach ($pairs as $pair) {
            if ($this->checkDirect($pair, $dto)) { // FROM/TO
                $direct = $pair;
                continue;
            }
            if ($this->checkReverse($pair, $dto)) { // TO/FROM
                $reverse = $pair;
                continue;
            }
            if ($this->checkFromDirect($pair, $dto)) { // FROM/...
                $fromDirect[$pair->to] = $pair;
                continue;
            }
            if ($this->checkToDirect($pair, $dto)) { // .../TO
                $toDirect[$pair->from] = $pair;
                continue;
            }
            if ($this->checkFromReverse($pair, $dto)) { // .../FROM
                $fromReverse[$pair->from] = $pair;
                continue;
            }
            if ($this->checkToReverse($pair, $dto)) { // TO/...
                $toReverse[$pair->to] = $pair;
            }
        }

        $paths = [];

        if (!is_null($direct)) {
            $paths[] = $this->createSingleDirectPath($direct);
        }
        if (!is_null($reverse)) {
            $paths[] = $this->createSingleReversePath($reverse);
        }

        $findInDirect = $this->findInFromDirect(
            $fromDirect,
            $toDirect,
            $toReverse
        );
        $findInReverse = $this->findInFromReverse(
            $fromReverse,
            $toReverse,
            $toDirect
        );

        $paths = array_merge($paths, $findInDirect, $findInReverse);
        return new Collection($paths);
    }

    private function createSingleDirectPath(PairDto $direct): PathToPairDto
    {
        return $this->createPathDto([
            $this->createPair($direct, OrderTypeEnum::BID)
        ]);
    }

    private function createSingleReversePath(PairDto $reverse): PathToPairDto
    {
        return $this->createPathDto([
            $this->createPair($reverse, OrderTypeEnum::ASK)
        ]);
    }

    private function findInFromDirect(array $fromDirect, array $toDirect, array $toReverse): array
    {
        $paths = [];

        // check FROM/... array and try find matches
        foreach ($fromDirect as $pair) {
            if (isset($toDirect[$pair->to])) { // match in .../TO array
                $secondPair = $this->createPair($toDirect[$pair->to], OrderTypeEnum::BID);
            } elseif (isset($toReverse[$pair->to])) { // match in TO/... array
                $secondPair = $this->createPair($toReverse[$pair->to], OrderTypeEnum::ASK);
            } else {
                continue;
            }

            $firstPair = $this->createPair($pair, OrderTypeEnum::BID);
            $paths[] = $this->createPathDto([$firstPair, $secondPair]);
        }

        return $paths;
    }

    private function findInFromReverse(array $fromReverse, array $toReverse, array $toDirect): array
    {
        $paths = [];

        // check .../FROM array and try find matches
        foreach ($fromReverse as $pair) {
            if (isset($toReverse[$pair->from])) { // match in TO/... array
                $secondPair = $this->createPair($toReverse[$pair->from], OrderTypeEnum::ASK);
            } elseif (isset($toDirect[$pair->from])) { // match in .../TO array
                $secondPair = $this->createPair($toDirect[$pair->from], OrderTypeEnum::BID);
            } else {
                continue;
            }

            $firstPair = $this->createPair($pair, OrderTypeEnum::ASK);
            $paths[] = $this->createPathDto([$firstPair, $secondPair]);
        }

        return $paths;
    }

    /**
     * @param PairWithDirectionDto[] $pairs
     * @return PathToPairDto
     */
    private function createPathDto(array $pairs): PathToPairDto
    {
        return new PathToPairDto(new Collection($pairs));
    }

    private function createPair(PairDto $pair, OrderTypeEnum $direction): PairWithDirectionDto
    {
        return new PairWithDirectionDto(
            $pair->from,
            $pair->to,
            $direction
        );
    }

    private function checkFromDirect(PairDto $pair, FindRateDto $dto): bool
    {
        return $dto->from === $pair->from;
    }

    private function checkFromReverse(PairDto $pair, FindRateDto $dto): bool
    {
        return $dto->from === $pair->to;
    }

    private function checkToDirect(PairDto $pair, FindRateDto $dto): bool
    {
        return $dto->to === $pair->to;
    }

    private function checkToReverse(PairDto $pair, FindRateDto $dto): bool
    {
        return $dto->to === $pair->from;
    }

    private function checkDirect(PairDto $pair, FindRateDto $dto): bool
    {
        return $dto->from === $pair->from &&
            $dto->to === $pair->to;
    }

    private function checkReverse(PairDto $pair, FindRateDto $dto): bool
    {
        return $dto->to === $pair->from &&
            $dto->from === $pair->to;
    }
}
