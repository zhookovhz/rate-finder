<?php

declare(strict_types=1);

namespace Modules\RateFinder\Services;

use Illuminate\Support\Collection;
use Modules\RateFinder\Data\FindRateDto;
use Modules\Shared\Data\OrderBook\OrderTypeEnum;
use Modules\Shared\Data\OrderBook\PathToPairDto;
use Modules\Shared\Data\PairDto;
use Modules\Shared\Data\PairWithDirectionDto;

class PathFinder
{
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
        };

        $paths = [];
        if (!is_null($direct)) {
            $pairs = [new PairWithDirectionDto($direct->from, $direct->to, OrderTypeEnum::BID)];
            $paths[] = new PathToPairDto(new Collection($pairs));
        }
        if (!is_null($reverse)) {
            $pairs = [new PairWithDirectionDto($reverse->from, $reverse->to, OrderTypeEnum::ASK)];
            $paths[] = new PathToPairDto(new Collection($pairs));
        }

        foreach ($fromDirect as $pair) {
            if (isset($toDirect[$pair->to])) {
                $pairs = [];
                $pairs[] = new PairWithDirectionDto(
                    $pair->from,
                    $pair->to,
                    OrderTypeEnum::BID
                );
                $pairs[] = new PairWithDirectionDto(
                    $toDirect[$pair->to]->from,
                    $toDirect[$pair->to]->to,
                    OrderTypeEnum::BID
                );
                $paths[] = new PathToPairDto(new Collection($pairs));
            }

            if (isset($toReverse[$pair->to])) {
                $pairs = [];
                $pairs[] = new PairWithDirectionDto(
                    $pair->from,
                    $pair->to,
                    OrderTypeEnum::BID
                );
                $pairs[] = new PairWithDirectionDto(
                    $toReverse[$pair->to]->from,
                    $toReverse[$pair->to]->to,
                    OrderTypeEnum::ASK
                );
                $paths[] = new PathToPairDto(new Collection($pairs));
            }
        }

        foreach ($fromReverse as $pair) {
            if (isset($toReverse[$pair->from])) {
                $pairs = [];
                $pairs[] = new PairWithDirectionDto(
                    $pair->from,
                    $pair->to,
                    OrderTypeEnum::ASK
                );
                $pairs[] = new PairWithDirectionDto(
                    $toReverse[$pair->from]->from,
                    $toReverse[$pair->from]->to,
                    OrderTypeEnum::ASK
                );
                $paths[] = new PathToPairDto(new Collection($pairs));
            }

            if (isset($toDirect[$pair->from])) {
                $pairs = [];
                $pairs[] = new PairWithDirectionDto(
                    $pair->from,
                    $pair->to,
                    OrderTypeEnum::ASK
                );
                $pairs[] = new PairWithDirectionDto(
                    $toDirect[$pair->from]->from,
                    $toDirect[$pair->from]->to,
                    OrderTypeEnum::BID
                );
                $paths[] = new PathToPairDto(new Collection($pairs));
            }
        }

        return new Collection($paths);
    }


    private function checkFromDirect(PairDto $pair, FindRateDto $dto): bool
    {
        return $dto->from->value === $pair->from;
    }

    private function checkFromReverse(PairDto $pair, FindRateDto $dto): bool
    {
        return $dto->from->value === $pair->to;
    }

    private function checkToDirect(PairDto $pair, FindRateDto $dto): bool
    {
        return $dto->to->value === $pair->to;
    }

    private function checkToReverse(PairDto $pair, FindRateDto $dto): bool
    {
        return $dto->to->value === $pair->from;
    }

    private function checkDirect(PairDto $pair, FindRateDto $dto): bool
    {
        return $dto->from->value === $pair->from &&
            $dto->to->value === $pair->to;
    }

    private function checkReverse(PairDto $pair, FindRateDto $dto): bool
    {
        return $dto->to->value === $pair->from &&
            $dto->from->value === $pair->to;
    }
}
