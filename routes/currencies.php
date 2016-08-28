<?php

use App\RequestDetailsParser;
use App\Currency;
use App\CurrencyTransformer;


use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/currencies", function ($request, $response, $arguments) use ($app) {
    $requestDetailsParser = new RequestDetailsParser($request);
    $currencies = $this->spot->mapper("App\Currency")
        ->all([
            "user_id" => $this->token->getUserId()
        ])
        ->order(["modified" => "DESC"])
        ->limit($requestDetailsParser->getLimit(),$requestDetailsParser->getOffset());

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Collection($currencies, new CurrencyTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
