<?php

declare(strict_types=1);

namespace App\Http\Api\Rate\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\RateFinder\Data\FindRateDto;

class GetRatesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from' => ['string', 'required'],
            'to' => ['string', 'required'],
            'amount' => ['numeric', 'required'],
        ];
    }

    public function toDto(): FindRateDto
    {
        return new FindRateDto(
            $this->input('from'),
            $this->input('to'),
            (float) $this->input('amount'),
        );
    }
}
