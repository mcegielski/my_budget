<?php

/*
 * This file is part of the Slim API skeleton package
 *
 * Copyright (c) 2016 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-api-skeleton
 *
 */

namespace App;

use App\Todo;
use League\Fractal;

class CurrencyTransformer extends Fractal\TransformerAbstract
{

    public function transform(Currency $currency)
    {
        return [
            "id" => (integer)$currency->id,
            "name" => (string)$currency->name ?: "",
            "modified" => $currency->modified->format('Y-m-d H:i:s'),
            "created" => $currency->created->format('Y-m-d H:i:s')
        ];
    }
}