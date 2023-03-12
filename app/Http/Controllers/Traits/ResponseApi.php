<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use \Illuminate\Validation\ValidationException;
use Illuminate\Support\MessageBag;

trait ResponseApi
{

    /**
     * Return a JSON response with status, message and data.
     *
     * @param mixed $data The data to be returned in the response.
     * @param string $message A message describing the response.
     * @param int $code The HTTP response code to be returned.
     * @param string $status The status of the response (e.g. "success", "error").
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseData($data, $message, int $code = 200, string $status = 'success'): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return a JSON response with status, message and errors.
     *
     * @param \Exception $e The exception that occurred.
     * @param string $message A message describing the error.
     * @return \Illuminate\Http\JsonResponse A JSON response.
     */
    public function responseError(\Exception $e, string $message)
    {
        $errorCodes = [
            BadRequestHttpException::class => 400,
            UnauthorizedHttpException::class => 401,
            ValidationException::class => 422,
            ModelNotFoundException::class => 404,
        ];

        $code = $errorCodes[get_class($e)] ?? 500;

        return $this->getError($this->newMessageBag($e), $message, $code);
    }

    /**
     * Generate a JSON response to represent an error.
     *
     * @param \Illuminate\Support\MessageBag $error The error message bag.
     * @param string $message A message describing the response.
     * @param int $code The HTTP status code for the response. [400-Bad Request, 401-Unauthorized, 422-Unprocessable Content, 404-Not Found, 500-Internal Server Error].
     * @param string $status The status type of the error message.
     * @return \Illuminate\Http\JsonResponse The JSON response with the error message.
     */
    public function getError(MessageBag $error, string $message, int $code = 400, string $status = 'error'): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'errors' => $error,
        ], $code);
    }

    /**
     * Create a new MessageBag object with an error message.
     *
     * @param string|\Exception $error The error message or exception that occurred.
     * @return \Illuminate\Support\MessageBag The MessageBag object with the error message.
     */
    public function newMessageBag($error): MessageBag
    {
        if ($error instanceof \Exception) {
            $description = $error->getMessage();
        } else {
            $description = $error;
        }
        return new MessageBag([ 'error' => $description ]);
    }

}
