<?php

namespace App\Filters;

use Illuminate\Support\Str;

class BaseFilter
{
    protected $model, $fields = [], $filterFields = [], $customSearch = [];

    public function qFilterFormatter($builder, $value, $fields)
    {
        $model = $this->model->getFillable();
        /* Start checking field is_active */
        $isActive = $this->fieldCheckIsActive($model);
        /* End checking field is_active */

        if (strtolower($value) === "active" and $isActive === true) {
            $builder->where('is_active', true);
        } else if ((strtolower($value) === "non-active" or strtolower($value) === "nonactive") and $isActive === true) {
            $builder->where('is_active', false);
        } else {
            $builder->where(function ($q) use ($fields, $value) {
                foreach ($fields as $idx => $field) {
                    /* Start check point */
                    $withRelation = explode('.', $field);
                    /* End check point */

                    if (count($withRelation) == 1) {
                        $q->orWhereRaw("UPPER(" . $field . ") LIKE ?", ["%" . strtoupper($value) . "%"]);
                    } else {
                        $q->orWhereHas($withRelation[0], function ($impQuery) use ($value, $withRelation, $field) {
                            if (isset($withRelation[2])) {
                                $impQuery->whereHas($withRelation[1], function ($impQuery) use ($value, $withRelation) {
                                    $impQuery->whereRaw("UPPER(" . $withRelation[2] . ") LIKE ?", ["%" . strtoupper($value) . '%']);
                                });
                            } else {
                                $impQuery->whereRaw("UPPER(" . $withRelation[1] . ") LIKE ?", ["%" . strtoupper($value) . '%']);
                            }
                        });
                    }
                }
            });
        }

        return $builder;
    }

    public function fieldCheckIsActive($models)
    {
        foreach ($models as $field) {
            if ($field == "is_active") {
                return true;
            }
        }
        return false;
    }

    public static function apply($query, $filters = null, $filterClass = null)
    {
        if ($filterClass !== null && $filters !== null) {
            foreach ($filters as $filterName => $value) {
                $method = static::createMethodName($filterName);
                if (static::isMethodExists($filterClass, $method)) {
                    $query = $filterClass->$method($query, $value);
                }
            }
        }

        return $query;
    }

    public static function createMethodName($method)
    {
        return Str::studly('filter' . $method);
    }

    public static function isMethodExists($class, $method)
    {
        return method_exists($class, $method);
    }

    // SORTIR LOGIC
    public function filterSort($query, $value)
    {
        $value = str_replace(" ", "", $value);
        $fillables = $this->model->getFillable();
        $fillables = array_flip($fillables);

        if (strpos($value, ",") !== false) {
            $explodes = explode(",", $value);
            foreach ($explodes as $idx => $field) {
                $cleanField = str_replace("-", "", $field);
                if (!array_key_exists($cleanField, $fillables)) {
                    unset($explodes[$idx]);
                }
            }

            foreach ($explodes as $idx => $field) {
                $cleanField = str_replace("-", "", $field);
                $sortType = "ASC";
                if (strpos($field, "-") !== false) {
                    $sortType = "DESC";
                }

                $query = $query->withoutGlobalScope('order')->orderBy($cleanField, $sortType);
            }
        } else {
            $cleanField = str_replace("-", "", $value);
            $sortType = "ASC";
            if (array_key_exists($cleanField, $fillables)) {
                if (strpos($value, "-") !== false) {
                    $sortType = "DESC";
                }

                $query = $query->withoutGlobalScope('order')->orderBy($cleanField, $sortType);
            }
        }
        return $query;
    }

    // New function for datatables start
    public static function createDtMethodName($method)
    {
        return Str::studly('dtFilter' . $method);
    }

    public static function createDtSearchMethodName($method)
    {
        return Str::studly('dtSearch' . $method);
    }

    public static function dtApply($query, $filterClass = null)
    {
        if ($filterClass !== null) {
            foreach ($filterClass->getFilterFields() as $filterName) {
                if (request()->$filterName) {
                    $method = static::createDtMethodName($filterName);
                    if (static::isMethodExists($filterClass, $method)) {
                        $query = $filterClass->$method($query, request()->$filterName);
                    } else {
                        $query = $query->where($filterName, '=', request()->$filterName);
                    }
                }
            }
        }

        return $query;
    }

    public static function dtCustomSearch($builder, $filterClass)
    {
        if ($filterClass !== null) {
            foreach ($filterClass->getCustomSearch() as $filterName) {
                $method = static::createDtSearchMethodName($filterName);
                if (static::isMethodExists($filterClass, $method)) {
                    $builder = $builder->filterColumn($filterName, function ($qry, $search) use ($filterClass, $method) {
                        $qry = $filterClass->$method($qry, $search);
                    });
                }
            }
        }

        return $builder;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getFilterFields()
    {
        return $this->filterFields;
    }

    public function getCustomSearch()
    {
        return $this->customSearch;
    }
}
