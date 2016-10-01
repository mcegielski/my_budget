<?php

namespace App;

class Category extends \App\RootEntity
{
    protected static $table = "categories";

    public static function fields()
    {
        $specificData = [
            "name" => ["type" => "string", "length" => 300],
            "user_id" => ["type" => "integer"]
        ];
        return array_merge($specificData, parent::fields());
    }
}
