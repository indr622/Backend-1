<?php

namespace App\Services;

use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BaseService
{
    use ApiResponseTrait;

    protected
        $repo,
        $object,
        $filterClass,
        $indexWith = [],
        $detailWith = [],
        $uploadFolder,
        $connection;

    public function __construct()
    {
        $this->connection = config('database.default');
    }

    public function getDatatables()
    {
        return $this->repo->getDatatables($this->filterClass);
    }

    public function all(array $request = null)
    {
        $datas = $this->repo->with($this->indexWith)->all($request, $this->filterClass);
        $success = $datas;
        return $this->successResponse($success, __('content.message.default.success'));
    }

    public function getData($id)
    {
        $data = $this->repo->with($this->detailWith)->find($id);
        $success['data'] = $data;

        return $this->successResponse($success, __('content.message.default.success'));
    }

    public function create(array $data)
    {
        try {
            $execute = DB::transaction(function () use ($data) {
                $created = $this->repo->create($data);
                return $created->refresh();
            });

            $success['data'] = $execute;
            return $this->successResponse($success, __('content.message.create.success'), 201);
        } catch (Exception $exc) {
            Log::error($exc);
            $failed['exception'] = $exc->getMessage();
            return $this->failedResponse(null, __('content.message.create.failed'), 400, $failed);
        }
    }

    public function update(array $data, $id)
    {
        try {
            $execute = DB::transaction(function () use ($data, $id) {
                $updated = $this->repo->update($data, $id);
                return $updated;
            });

            $success['data'] = $execute;
            return $this->successResponse($success, __('content.message.update.success'));
        } catch (Exception $exc) {
            Log::error($exc);
            $failed['exception'] = $exc->getMessage();
            return $this->failedResponse(null, __('content.message.create.failed'), 400, $failed);
        }
    }



    public function restore($id)
    {
        try {
            $execute = DB::transaction(function () use ($id) {
                return $this->repo->withTrashed()->find($id)->restore();
            });

            $success['data'] = $execute;
            return $this->successResponse($success, __('content.message.update.success'));
        } catch (Exception $e) {
            Log::error($e);
            $failed['exception'] = $e->getMessage();
            return $this->failedResponse(null, __('content.message.create.failed'), 400, $failed);
        }
    }

    public function destroy($id)
    {
        try {
            $execute = DB::transaction(function () use ($id) {
                return $this->repo->withTrashed()->find($id)->destroy();
            });

            $success['data'] = $execute;
            return $this->successResponse($success, __('content.message.delete.success'));
        } catch (Exception $e) {
            Log::error($e);
            $failed['exception'] = $e->getMessage();
            return $this->failedResponse(null, __('content.message.delete.failed'), 400, $failed);
        }
    }

    public function uploadFile(array $data, $key, $rename = false, $filename = "")
    {
        if (isset($data[$key])) {
            $file = $data[$key];
            $filepath = 'public/' . $this->uploadFolder;
            $extension = $file->extension();

            $uploadedFile = Storage::put($filepath, $file);

            if ($rename && $filename !== "") {
                $newFilename = $filepath . '/' . $filename . '.' . $extension;
                if (Storage::exists($newFilename)) {
                    Storage::delete($newFilename);
                }
                Storage::move($uploadedFile, $newFilename);
            } else {
                $newFilename = $uploadedFile;
            }

            $data[$key] = $newFilename;
        }

        return $data;
    }

    public function beginTransaction($conn = null): void
    {
        DB::connection($conn ?? $this->connection)->beginTransaction();
    }

    public function commit($conn = null): void
    {
        DB::connection($conn ?? $this->connection)->commit();
    }

    public function rollBack($conn = null): void
    {
        DB::connection($conn ?? $this->connection)->rollBack();
    }
}
