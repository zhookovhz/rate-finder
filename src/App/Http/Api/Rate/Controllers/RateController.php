<?php

declare(strict_types=1);

namespace App\Http\Api\Rate\Controllers;

use App\Http\Api\Rate\Requests\GetRatesRequest;
use App\Http\Api\Rate\Resources\RateDtoCollectionResources;
use App\Http\Controller;
use Modules\RateFinder\Contracts\RateFinderServiceInterface;

class RateController extends Controller
{
    public function __construct(private readonly RateFinderServiceInterface $rateFinderService)
    {
    }

    public function get(GetRatesRequest $request): RateDtoCollectionResources
    {
        $rates = $this->rateFinderService->find($request->toDto());
        return new RateDtoCollectionResources($rates);
    }
}
