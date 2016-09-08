<?php
namespace App;

use App\Todo;
use League\Fractal;

class CategoryTransformer extends RootTransformer
{

    public function transform(Category $category)
    {
        return array_merge([
            "name" => (string)$category->name ?: ""
        ], parent::transform($category));
    }
}