<?php

namespace App\Providers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!Collection::hasMacro('paginate')) {

            Collection::macro(
                'paginate',
                function ($perPage = 15, $page = null, $options = []) {
                    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
                    $lap = (new LengthAwarePaginator(
                        $this->forPage($page, $perPage),
                        $this->count(),
                        $perPage,
                        $page,
                        $options
                    ));

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
            );
        }
    }
}
