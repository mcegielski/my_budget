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

class Currency extends \Spot\Entity
{
    protected static $table = "currencies";

    public static function fields()
    {
        return [
            "id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
            "name" => ["type" => "string", "length" => 300],
            "user_id" => ["type" => "integer"],
            "created"   => ["type" => "datetime"],
            "modified"   => ["type" => "datetime"]
        ];
    }

    public static function events(EventEmitter $emitter)
    {
        $emitter->on("beforeUpdate", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->modified = new \DateTime();
        });
        $emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
            $entity->created = new \DateTime();
            $entity->modified = new \DateTime();
        });
    }
}
