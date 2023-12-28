<?php

namespace App\Traits;

use Error;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponseTrait
{
    /**
     * @param JsonResource $resource
     * @param null $message
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    protected function respondWithResource(JsonResource $resource, $message = null, $statusCode = 200, $headers = [])
    {
        return $this->apiResponse(
            [
                'result' => $resource,
                'message' => $message
            ],
            $statusCode,
            $headers
        );
    }

    /**
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     * @return array
     */
    public function parseGivenData($data = [], $statusCode = 200, $headers = [])
    {
        $responseStructure = [
            'message' => $data['message'] ?? null,
            'result' => $data['result'] ?? null,
        ];

        if (array_key_exists('errors', $data)) {
            $responseStructure['errors'] = $data['errors'];
        }

        if (isset($data['status'])) {
            $statusCode = $data['status'];
        }


        if (isset($data['exception']) && ($data['exception'] instanceof Error || $data['exception'] instanceof Exception)) {
            if (config('app.env') !== 'production') {
                $responseStructure['exception'] = [
                    'message' => $data['exception']->getMessage(),
                    'file' => $data['exception']->getFile(),
                    'line' => $data['exception']->getLine(),
                    'code' => $data['exception']->getCode(),
                    'trace' => $data['exception']->getTrace(),
                ];
            }

            if ($statusCode === 200) {
                $statusCode = 500;
            }
        }

        return ["content" => $responseStructure, "statusCode" => $statusCode, "headers" => $headers];
    }

    /**
     * Return generic json response with the given data.
     *
     * @param       $data
     * @param int $statusCode
     * @param array $headers
     *
     * @return JsonResponse
     */
    protected function apiResponse($data = [], $statusCode = 200, $headers = []): JsonResponse
    {
        $result = $this->parseGivenData($data, $statusCode, $headers);

        return response()->json(
            $result['content'],
            $result['statusCode'],
            $result['headers']
        );
    }

    public function successResponse($result, $message = '', $statusCode = 200): JsonResponse
    {
        return $this->apiResponse(
            [
                'message' => ($message == '') ? __('content.message.default.success') : $message,
                'result' => $result,
                'errors' => null
            ],
            $statusCode
        );
    }

    public function failedResponse($result = null, $message = '', $statusCode = 400, $errors = null): JsonResponse
    {
        return $this->apiResponse(
            [
                'message' => ($message == '') ? __('content.message.default.failed') : $message,
                'result' => $result,
                'errors' => $errors
            ],
            $statusCode
        );
    }
}
