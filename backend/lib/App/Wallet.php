<?php

namespace App;

class Wallet extends \App\RootEntity
{
    protected static $table = "wallets";

    public static function fields()
    {
        $specificData = [
            "name" => ["type" => "string", "length" => 300],
            "currency_id" => ["type" => "integer"],
            "balance" => ["type" => "decimal"]
        ];
        return array_merge($specificData, parent::fields());
    }
}
