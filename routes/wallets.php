<?php

use App\RequestDetailsParser;
use App\Wallet;
use App\WalletTransformer;

use Exception\NotFoundException;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/wallets", function ($request, $response, $arguments) use ($app) {
    $requestDetailsParser = new RequestDetailsParser($request);
    $wallets = $this->spot->mapper("App\Wallet")
        ->query("SELECT w.*
                FROM wallets w
                    JOIN currencies c ON w.currency_id = c.id
                WHERE c.user_id = :user_id
                ORDER BY w.".$requestDetailsParser->getOrderBy(Wallet::fields())." ".$requestDetailsParser->getOrderHow()."
                LIMIT ".$requestDetailsParser->getOffset().", ".$requestDetailsParser->getLimit(),
                ["user_id" => $this->token->getUserId()]);
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Collection($wallets, new WalletTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


$app->post("/wallets", function ($request, $response, $arguments) {
    $body = $request->getParsedBody();
    $wallet = new Wallet($body);
    
    $currency = $this->spot->mapper("App\Currency")
        ->first([
            "user_id" => $this->token->getUserId(),
            "id" => $wallet->currency_id
        ]);
    if (!$currency){
        return $response->withStatus(403)->write("Not owned linked resources selected!");
    }
    if (!$wallet->balance){
        $wallet->balance = 0;
    }
    $this->spot->mapper("App\Wallet")->save($wallet);
    
    
    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($wallet, new WalletTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "New wallet created";
    
    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/wallets/{id}", function ($request, $response, $arguments) {
    $wallet = $this->spot->mapper("App\Wallet")
        ->query("SELECT w.*
                FROM wallets w
                    JOIN currencies c ON w.currency_id = c.id
                WHERE w.id = :id AND c.user_id = :user_id",
                ["id" => $arguments["id"], "user_id" => $this->token->getUserId()])[0];
    
    if (!$wallet) {
        throw new NotFoundException("Wallet not found.", 404);
    };

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($wallet, new WalletTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->put("/wallets/{id}", function ($request, $response, $arguments) {
    $wallet = $this->spot->mapper("App\Wallet")
        ->query("SELECT w.*
                FROM wallets w
                    JOIN currencies c ON w.currency_id = c.id
                WHERE w.id = :id AND c.user_id = :user_id",
                ["id" => $arguments["id"], "user_id" => $this->token->getUserId()])[0];
    
    if (!$wallet) {
        throw new NotFoundException("Wallet not found.", 404);
    };
    $body = $request->getParsedBody();
    $wallet->data($body);
    $this->spot->mapper("App\Wallet")->save($wallet);
    
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($wallet, new WalletTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "Wallet updated";
    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/wallets/{id}", function ($request, $response, $arguments) {
    $wallet = $this->spot->mapper("App\Wallet")
        ->query("SELECT w.*
                FROM wallets w
                    JOIN currencies c ON w.currency_id = c.id
                WHERE w.id = :id AND c.user_id = :user_id",
                ["id" => $arguments["id"], "user_id" => $this->token->getUserId()])[0];
    if (!$wallet) {
        throw new NotFoundException("Wallet not found.", 404);
    };

    $this->spot->mapper("App\Wallet")->delete($wallet);

    $data["status"] = "ok";
    $data["message"] = "Wallet deleted";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});