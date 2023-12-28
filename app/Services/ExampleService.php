<?php

namespace App\Services;

use App\Exceptions\ActionException;
use App\Filters\ExampleFilter;
use App\Repositories\ExampleRepository;
use App\Services\BaseService;
use Illuminate\Http\Request;
use stdClass;

class ExampleService extends BaseService
{
    public function __construct(
        ExampleRepository $repo,
        ExampleFilter $filterClass
    ) {
        parent::__construct();
        $this->repo = $repo;
        $this->object = 'object Name';
        $this->filterClass = $filterClass;
    }

    public function success(Request $request)
    {
        try {
            // Success logic here
            return $this->successResponse(null);
        } catch (ActionException $e) {
            // Error Logic Here
            return $this->failedResponse(null, $e->getMessage(), $e->getCode());
        } catch (\Throwable $th) {
            // Error Server Here
            logError('Success Example Controller', $th);

            return $this->failedResponse(null, 'Server Encountered an Error, please contact your IT Administrator', 500);
        }
    }

    public function successWithData(Request $request)
    {
        try {
            // Success logic here
            return $this->successResponse(['data' => 'Dummy Data'], 'Set your custom message here');
        } catch (ActionException $e) {
            // Error Logic Here
            return $this->failedResponse(null, $e->getMessage(), $e->getCode());
        } catch (\Throwable $th) {
            // Error Server Here
            logError('Success with Data Example Controller', $th);

            return $this->failedResponse(null, 'Server Encountered an Error, please contact your IT Administrator', 500);
        }
    }

    public function failedLogic(Request $request)
    {
        try {
            // Check if logic is allowed or not
            if (true) {
                // If true then throw an error, use ActionException class to differ Logic error from Server error
                throw new ActionException('Unauthorized', 401);
            }

            // Success logic here
            return $this->successResponse(['data' => 'Dummy Data'], 'Set your custom message here');
        } catch (ActionException $e) {
            // Error Logic Here
            return $this->failedResponse(null, $e->getMessage(), $e->getCode());
        } catch (\Throwable $th) {
            // Error Server Here
            logError('Failed logic Example Controller', $th);

            return $this->failedResponse(null, 'Server Encountered an Error, please contact your IT Administrator', 500);
        }
    }

    public function failedServer(Request $request)
    {
        try {
            // Set always error on code here so we can check if system error handler is working
            $data = new stdClass();
            $temp = $data->hello;

            // Success logic here
            return $this->successResponse(['data' => 'Dummy Data'], 'Set your custom message here');
        } catch (ActionException $e) {
            // Error Logic Here
            return $this->failedResponse(null, $e->getMessage(), $e->getCode());
        } catch (\Throwable $th) {
            // Error Server Here
            logError('Failed logic Example Controller', $th);

            return $this->failedResponse(null, 'Server Encountered an Error, please contact your IT Administrator', 500);
        }
    }
}
