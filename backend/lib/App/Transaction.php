<?php

namespace App;

class Transaction extends \App\RootEntity
{
    protected static $table = "transactions";
    
    public function data($data = null, $modified = true)
    {
        //FIXME: better place to handle date conversion? Why does not work?
        //if (!($data instanceof DateTime)) {
        //    $data["date"] = \DateTime::createFromFormat('Y-m-d', $data["date"]);
        //}
        return parent::data($data, $modified);    
    }
    
    public static function fields()
    {
        $specificData = [
            "name" => ["type" => "string", "length" => 300],
            "wallet_from_id" => ["type" => "integer"],
            "value" => ["type" => "decimal"],
            "date" => ["type" => "date"],
            "wallet_to_id" => ["type" => "integer"],
            "value_to" => ["type" => "decimal"]
        ];
        return array_merge($specificData, parent::fields());
    }
}
