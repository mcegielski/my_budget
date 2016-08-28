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

use Spot\EntityInterface;
use Spot\MapperInterface;
use Spot\EventEmitter;

use Tuupola\Base62;

use Ramsey\Uuid\Uuid;
use Psr\Log\LogLevel;

class User extends \Spot\Entity
{
    protected static $table = "users";

    public static function fields()
    {
        return [
            "id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
            "email" => ["type" => "string", "length" => 300],
            "hash" => ["type" => "string", "length" => 255]
//             ,"created"   => ["type" => "datetime", "value" => new \DateTime()],
//             ,"updated"   => ["type" => "datetime", "value" => new \DateTime()]
        ];
    }

    public static function events(EventEmitter $emitter)
    {
        $emitter->on("beforeUpdate", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->updated_at = new \DateTime();
        });
    }
//     public function timestamp()
//     {
//         return $this->updated_at->getTimestamp();
//     }
// 
//     public function etag()
//     {
//         return md5($this->uid . $this->timestamp());
//     }
// 
//     public function clear()
//     {
//         $this->data([
//             "order" => null,
//             "title" => null,
//             "completed" => null
//         ]);
//     }
}
