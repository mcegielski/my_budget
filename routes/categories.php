<?php

use App\RequestDetailsParser;
use App\Category;
use App\CategoryTransformer;

use Exception\NotFoundException;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/categories", function ($request, $response, $arguments) use ($app) {
    $requestDetailsParser = new RequestDetailsParser($request);
    $categories = $this->spot->mapper("App\Category")
        ->all([
            "user_id" => $this->token->getUserId()
        ])
        ->order($requestDetailsParser->getOrder())
        ->limit($requestDetailsParser->getLimit(),$requestDetailsParser->getOffset());

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Collection($categories, new CategoryTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


$app->post("/categories", function ($request, $response, $arguments) {
    $body = $request->getParsedBody();

    $category = new Category($body);
    $category->user_id = $this->token->getUserId();
    $this->spot->mapper("App\Category")->save($category);


    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($category, new CategoryTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "New currency created";

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/categories/{id}", function ($request, $response, $arguments) {
    if (false === $category = $this->spot->mapper("App\Category")->first([
        "user_id" => $this->token->getUserId(),
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("Category not found.", 404);
    };

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($category, new CategoryTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->put("/categories/{id}", function ($request, $response, $arguments) {
    if (false === $category = $this->spot->mapper("App\Category")->first([
        "user_id" => $this->token->getUserId(),
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("Category not found.", 404);
    };
    $body = $request->getParsedBody();
    /* PUT request assumes full representation. If any of the properties is */
    /* missing set them to default values by clearing the todo object first. */
    //$category->clear();
    $category->data($body);
    $this->spot->mapper("App\Category")->save($category);
    
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($category, new CategoryTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "Category updated";
    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/categories/{id}", function ($request, $response, $arguments) {
    if (false === $category = $this->spot->mapper("App\Category")->first([
        "user_id" => $this->token->getUserId(),
        "id" => $arguments["id"]
    ])) {
        throw new NotFoundException("Category not found.", 404);
    };

    $this->spot->mapper("App\Category")->delete($category);

    $data["status"] = "ok";
    $data["message"] = "Category deleted";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});