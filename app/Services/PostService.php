<?php

namespace App\Services;

use Exception;
use App\Filters\PostFilter;
use App\Services\BaseService;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Repositories\PostRepository;
use Symfony\Component\HttpFoundation\Response;

class PostService extends BaseService
{
    use ApiResponseTrait;
    public function __construct(PostRepository $repo, PostFilter $filterClass)
    {
        parent::__construct();
        $this->repo = $repo;
        $this->object = 'object Name';
        $this->filterClass = $filterClass;
    }

    public function index($request)
    {
        try {
            $posts = $this->repo->getAll($request);
            return $this->successResponse(
                $posts,
                'Posts retrieved successfully',
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            Log::error("Index Post :" . $e->getMessage());
            return $this->failedResponse(
                null,
                'Action failed',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $e->getMessage()
            );
        }
    }

    public function show($id)
    {
        try {
            $post = $this->repo->find($id);
            return $this->successResponse(
                $post,
                'Post retrieved successfully',
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            Log::error("Show Post :" . $e->getMessage());
            return $this->failedResponse(
                null,
                'Action failed',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $e->getMessage()
            );
        }
    }

    public function store(array $data)
    {
        $data['user_id'] = auth()->user()->id;

        try {
            $post = $this->repo->create($data);
            return $this->successResponse(
                $post,
                'Post created successfully',
                Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            Log::error("Store Post :" . $e->getMessage());
            return $this->failedResponse(
                null,
                'Action failed',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $e->getMessage()
            );
        }
    }

    public function update($request, $id)
    {
        try {
            $post = $this->repo->find($id);
            $post->title = $request->title;
            $post->body = $request->body;
            $post->save();
            return $this->successResponse(
                $post,
                'Post updated successfully',
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            Log::error("Update Post :" . $e->getMessage());
            return $this->failedResponse(
                null,
                'Action failed',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $e->getMessage()
            );
        }
    }

    public function delete($id)
    {
        try {
            $post = $this->repo->find($id);
            $post->delete();
            return $this->successResponse(
                $post,
                'Post deleted successfully',
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            Log::error("Delete Post :" . $e->getMessage());
            return $this->failedResponse(
                null,
                'Action failed',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $e->getMessage()
            );
        }
    }
}
