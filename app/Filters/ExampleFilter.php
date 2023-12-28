<?php

namespace App\Filters;

use App\Filters\BaseFilter;
use App\Models\User;

class ExampleFilter extends BaseFilter
{
    public function __construct(User $model)
    {
        $this->model = $model;
        $this->fields = [
            'email',
            'name',
            'created_at',
            'updated_at'
        ];

        $this->filterFields = [
            'email',
            'name',
            'created_at',
            'updated_at'
        ];

        $this->customSearch = [
            'name',
        ];
    }

    public function filterQ($builder, $value)
    {
        $builder = $this->qFilterFormatter($builder, $value, $this->fields);
        return $builder;
    }

    // Digunakan untuk filter data datatable
    public function dtFilterName($builder, $search)
    {
        return $builder->where('name', $search);
    }

    // Digunakan untuk override search datatable
    public function dtSearchName($builder, $search)
    {
        return $builder->where('name', 'LIKE', '%' . $search . '%');
    }
}
