<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\BaseRepository;

class PostRepository extends BaseRepository
{
    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    public function getAll($request)
    {
        $builder = $this->model->with(['user' => function ($query) {
            $query->select('id', 'name', 'email');
        }]);
        return $builder->paginate($request->per_page ?? 10);
    }

    public function getById(string $id)
    {
        return $this->model->findOrFail($id);
    }
}
