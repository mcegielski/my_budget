<?php

use App\RequestDetailsParser;
use App\Transaction;
use App\TransactionTransformer;

use Exception\NotFoundException;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/transactions", function ($request, $response, $arguments) use ($app) {
    $requestDetailsParser = new RequestDetailsParser($request);
    $transactions = $this->spot->mapper("App\Transaction")
        ->query("SELECT t.*
                    FROM transactions t
                    JOIN wallets w ON t.wallet_from_id = w.id
                    JOIN currencies c ON w.currency_id = c.id
                    WHERE c.user_id = :user_id
                ORDER BY w.".$requestDetailsParser->getOrderBy(Transaction::fields())." ".$requestDetailsParser->getOrderHow()."
                LIMIT ".$requestDetailsParser->getOffset().", ".$requestDetailsParser->getLimit(),
                ["user_id" => $this->token->getUserId()]);
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Collection($transactions, new TransactionTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});


$app->post("/transactions", function ($request, $response, $arguments) {
    $body = $request->getParsedBody();
    $body["date"] = DateTime::createFromFormat('Y-m-d', $body["date"]);
    $transaction = new Transaction($body);
    
    $walletFrom = $this->spot->mapper("App\Wallet")
        ->query("SELECT w.*
                FROM wallets w
                    JOIN currencies c ON w.currency_id = c.id
                WHERE w.id = :id AND c.user_id = :user_id",
                ["id" => $transaction->wallet_from_id, "user_id" => $this->token->getUserId()])[0];
    if (!$walletFrom){
        return $response->withStatus(403)->write("Not owned linked resources selected!");
    }
    
    if ($transaction->wallet_to_id){
        $walletTo = $this->spot->mapper("App\Wallet")
        ->query("SELECT w.*
                FROM wallets w
                    JOIN currencies c ON w.currency_id = c.id
                WHERE w.id = :id AND c.user_id = :user_id",
                ["id" => $transaction->wallet_to_id, "user_id" => $this->token->getUserId()])[0];
        if (!$walletTo){
            return $response->withStatus(403)->write("Not owned linked resources selected!");
        }
    }
    $this->spot->mapper("App\Transaction")->save($transaction);
    
    
    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($transaction, new TransactionTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "New transaction created";
    
    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/transactions/{id}", function ($request, $response, $arguments) {
    $transaction = $this->spot->mapper("App\Transaction")
        ->query("SELECT t.*
                FROM transactions t
                    JOIN wallets w on t.wallet_from_id
                    JOIN currencies c ON w.currency_id = c.id
                WHERE t.id = :id AND c.user_id = :user_id",
                ["id" => $arguments["id"], "user_id" => $this->token->getUserId()])[0];
    
    if (!$transaction) {
        throw new NotFoundException("Transaction not found.", 404);
    };

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($transaction, new TransactionTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->put("/transactions/{id}", function ($request, $response, $arguments) {
    $transaction = $this->spot->mapper("App\Transaction")
        ->query("SELECT t.*
                FROM transactions t
                    JOIN wallets w on t.wallet_from_id
                    JOIN currencies c ON w.currency_id = c.id
                WHERE t.id = :id AND c.user_id = :user_id",
                ["id" => $arguments["id"], "user_id" => $this->token->getUserId()])[0];
    
    if (!$transaction) {
        throw new NotFoundException("Transaction not found.", 404);
    };
    
    $body = $request->getParsedBody();
    $body["date"] = DateTime::createFromFormat('Y-m-d', $body["date"]);
    $transaction->data($body);
    $this->spot->mapper("App\Transaction")->save($transaction);
    
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($transaction, new TransactionTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "Transaction updated";
    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/transactions/{id}", function ($request, $response, $arguments) {
    $transaction = $this->spot->mapper("App\Transaction")
        ->query("SELECT t.*
                FROM transactions t
                    JOIN wallets w on t.wallet_from_id
                    JOIN currencies c ON w.currency_id = c.id
                WHERE t.id = :id AND c.user_id = :user_id",
                ["id" => $arguments["id"], "user_id" => $this->token->getUserId()])[0];
    if (!$transaction) {
        throw new NotFoundException("Transaction not found.", 404);
    };

    $this->spot->mapper("App\Transaction")->delete($transaction);

    $data["status"] = "ok";
    $data["message"] = "Transaction deleted";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});