<?php

declare(strict_types=1);

namespace Modules\RateFinder\Services;

use Illuminate\Support\Collection;
use Modules\RateFinder\Data\FindRateDto;
use Modules\RateFinder\Data\PairWithDirectionDto;
use Modules\RateFinder\Data\PathToPairDto;
use Modules\Shared\Data\OrderBook\OrderTypeEnum;
use Modules\Shared\Data\PairDto;

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
            $this->createPairWithDirectionDto($direct, OrderTypeEnum::ASK)
        ]);
    }

    private function createSingleReversePath(PairDto $reverse): PathToPairDto
    {
        return $this->createPathDto([
            $this->createPairWithDirectionDto($reverse, OrderTypeEnum::BID)
        ]);
    }

    private function findInFromDirect(array $fromDirect, array $toDirect, array $toReverse): array
    {
        $paths = [];

        // check FROM/... array and try find matches
        foreach ($fromDirect as $pair) {
            if (isset($toDirect[$pair->to])) { // match in .../TO array
                $secondPair = $this->createPairWithDirectionDto($toDirect[$pair->to], OrderTypeEnum::ASK);
            } elseif (isset($toReverse[$pair->to])) { // match in TO/... array
                $secondPair = $this->createPairWithDirectionDto($toReverse[$pair->to], OrderTypeEnum::BID);
            } else {
                continue;
            }

            $firstPair = $this->createPairWithDirectionDto($pair, OrderTypeEnum::ASK);
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
                $secondPair = $this->createPairWithDirectionDto($toReverse[$pair->from], OrderTypeEnum::BID);
            } elseif (isset($toDirect[$pair->from])) { // match in .../TO array
                $secondPair = $this->createPairWithDirectionDto($toDirect[$pair->from], OrderTypeEnum::ASK);
            } else {
                continue;
            }

            $firstPair = $this->createPairWithDirectionDto($pair, OrderTypeEnum::BID);
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

    private function createPairWithDirectionDto(PairDto $pair, OrderTypeEnum $direction): PairWithDirectionDto
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
