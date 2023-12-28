<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class Datatable
{
    public static function handle($request)
    {
        if (array_key_exists('dt', $request)) {
            $limit = (int) $request['length'];
            $page     = ($limit != 0) ? ceil($request['start'] / $limit) + 1 : 1;
            $q = $request['search']['value'];

            $request['page'] = $page;
            $request['limit'] = $limit;
            $request['q'] = $q;


            $request = Datatable::getFilterColumn($request);
        }

        // $request = Datatable::getOrderColumn($request);

        return $request;
    }

    public static function getOrderColumn($request)
    {
        if (array_key_exists('order', $request) && array_key_exists('orderIndexValue', $request)) {
            foreach ($request['order'] as $key => $order) {
                $col = $order['column'];
                $dir = $order['dir'];

                if (array_key_exists($col, $request['orderIndexValue'])) {
                    $field = $request['orderIndexValue'][$col];
                    $sortVal = "";
                    if ($dir == "desc") {
                        $sortVal .= "-";
                    }

                    $sortVal .= $field;

                    $request['sort'] = $sortVal;
                }
            }
        }

        return $request;
    }

    public static function getFilterColumn($request)
    {
        if (array_key_exists('columns', $request)) {
            foreach ($request['columns'] as $key => $column) {
                $field = $column['data'];
                $searchValue = $column['search']['value'];
                if ($searchValue !== null) {
                    $fieldKey = static::trimFieldName($field);
                    $request[$fieldKey[0]] = $fieldKey[1] . $searchValue;
                }
            }
        }

        return $request;
    }

    public static function trimFieldName($fieldName)
    {
        $field = $fieldName;
        $relation = "";
        if (static::isContainDot($fieldName)) {
            $explodeField = explode(".", $fieldName);
            $field = $explodeField[sizeof($explodeField) - 1];
            $relation = static::getRelationString($fieldName) . ".";
        }

        if (static::isContaintUnderscore($field)) {
            $explodeField = explode("_", $field);
            $field = join("", array_map('ucfirst', explode("_", $field)));
        }

        return [$field, $relation];
    }

    public static function getRelationString($field)
    {
        $str = "";
        if (static::isContainDot($field)) {
            $explodeStr = explode(".", $field);
            $str = str_replace("." . $explodeStr[sizeof($explodeStr) - 1], "", $field);
        }

        return $str;
    }

    public static function isContainDot($string)
    {
        return strpos($string, ".");
    }

    public static function isContaintUnderscore($string)
    {
        return strpos($string, "_");
    }
}
