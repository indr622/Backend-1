<?php

namespace App\Filters;

use App\Filters\BaseFilter;
use App\Models\Post;

class PostFilter extends BaseFilter
{
    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    public function filterQ($builder, $value)
    {
        $fields = [];
        $builder = $this->qFilterFormatter($builder, $value, $fields);
        return $builder;
    }

    //
}
