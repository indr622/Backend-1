<?php

namespace App\Helpers;

use Illuminate\Pagination\Paginator;

class Pagination
{
    public static function paginate($query, $request = null)
    {
        if ($request !== null && (array_key_exists("page", $request) && static::isNumber($request['page']) && $request['page'] > 0)) {
            if (array_key_exists("limit", $request) && static::isNumber($request['limit'])) {
                return static::formattingPagination($query->paginate($request['limit']));
            }
            return static::formattingPagination($query->paginate(10));
        }
        return static::formattingPagination($query->paginate($query->get()->count()));
    }

    public static function isNumber($page)
    {
        return is_numeric($page);
    }

    public static function formattingPagination($pagination)
    {
        $lap = $pagination;

        return [
            'data' => $lap->values(),
            'links' => [
                'first' => $lap->url(1),
                'last' => $lap->url($lap->lastPage()),
                'prev' => $lap->previousPageUrl(),
                'next' => $lap->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $lap->currentPage(),
                'from' => $lap->firstItem(),
                'last_page' => $lap->lastPage(),
                'path' => Paginator::resolveCurrentPath(),
                'per_page' => $lap->perPage(),
                'to' => $lap->lastItem(),
                'total' => $lap->total(),
            ],
        ];
    }
}
