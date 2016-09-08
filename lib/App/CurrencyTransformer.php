<?php
namespace App;

use App\Todo;
use League\Fractal;

class CurrencyTransformer extends RootTransformer
{

    public function transform(Currency $currency)
    {
        return array_merge([
            "name" => (string)$currency->name ?: ""
        ], parent::transform($currency));
    }
}