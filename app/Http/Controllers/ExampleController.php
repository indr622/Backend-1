<?php

namespace App\Http\Controllers;

use App\Services\ExampleService;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function __construct(ExampleService $service)
    {
        $this->service = $service;
    }

    public function successResponseExample(Request $request)
    {
        return $this->service->success($request);
    }

    public function successResponseWithData(Request $request)
    {
        return $this->service->successWithData($request);
    }

    public function failedResponseExample(Request $request)
    {
        return $this->service->failedLogic($request);
    }

    public function errorResponse(Request $request)
    {
        return $this->service->failedServer($request);
    }
}
