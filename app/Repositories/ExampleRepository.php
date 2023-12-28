<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;

class ExampleRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }
}
