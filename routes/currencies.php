<?php

use App\RequestDetailsParser;
use App\Currency;
use App\CurrencyTransformer;

use Exception\NotFoundException;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/currencies", function ($request, $response, $arguments) use ($app) {
    $requestDetailsParser = new RequestDetailsParser($request);
    $currencies = $this->spot->mapper("App\Currency")
        ->all([
            "user_id" => $this->token->getUserId()
        ])
        ->order($requestDetailsParser->getOrder())
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


$app->post("/currencies", function ($request, $response, $arguments) {
    $body = $request->getParsedBody();

    $currency = new Currency($body);
    $currency->user_id = $this->token->getUserId();
    $this->spot->mapper("App\Currency")->save($currency);


    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($currency, new CurrencyTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "New currency created";

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/currencies/{id}", function ($request, $response, $arguments) {
    if (false === $currency = $this->spot->mapper("App\Currency")->first([
        "user_id" => $this->token->getUserId(),
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("Currency not found.", 404);
    };

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($currency, new CurrencyTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->put("/currencies/{id}", function ($request, $response, $arguments) {
    if (false === $currency = $this->spot->mapper("App\Currency")->first([
        "user_id" => $this->token->getUserId(),
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("Currency not found.", 404);
    };
    $body = $request->getParsedBody();
    /* PUT request assumes full representation. If any of the properties is */
    /* missing set them to default values by clearing the todo object first. */
    //$currency->clear();
    $currency->data($body);
    $this->spot->mapper("App\Currency")->save($currency);
    
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($currency, new CurrencyTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "Currency updated";
    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/currencies/{id}", function ($request, $response, $arguments) {
    if (false === $currency = $this->spot->mapper("App\Currency")->first([
        "user_id" => $this->token->getUserId(),
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("Currency not found.", 404);
    };

    $this->spot->mapper("App\Currency")->delete($currency);

    $data["status"] = "ok";
    $data["message"] = "Currency deleted";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});