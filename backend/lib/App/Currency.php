<?php

namespace App;

class Currency extends \App\RootEntity
{
    protected static $table = "currencies";

    public static function fields()
    {
        $specificData = [
            "name" => ["type" => "string", "length" => 300],
            "user_id" => ["type" => "integer"]
        ];
        return array_merge($specificData, parent::fields());
    }
}
