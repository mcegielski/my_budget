<?php
namespace App;

use App\Todo;
use League\Fractal;

class RootTransformer extends Fractal\TransformerAbstract
{

    public function transform(RootEntity $rootEntity)
    {
        return [
            "id" => (integer)$rootEntity->id,
            "modified" => $rootEntity->modified->format('Y-m-d H:i:s'),
            "created" => $rootEntity->created->format('Y-m-d H:i:s')
        ];
    }
}