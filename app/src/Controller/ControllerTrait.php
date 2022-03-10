<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * Trait ControllerTrait.
 */
trait ControllerTrait
{
    /**
     * Retourne une exception en json.
     *
     * @param      $e
     * @param null $code
     */
    protected function jsonException($e, $code = null): JsonResponse
    {
        $messages[] = $e->getMessage();
        while ($exception = $e->getPrevious()) {
            $messages[] = $exception->getMessage();
            $e = $exception;
        }

        return $this->json([
                               'error' => 'invalid_request',
                               'error_description' => $messages,
                           ], $code > 0 ? $code : Response::HTTP_BAD_REQUEST);
    }

    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
     *
     * @param $data
     */
    protected function json(
        $data,
        int $status = 200,
        array $headers = [],
        array $context = []
    ): JsonResponse {
        try {
            return new JsonResponse(
                $this->serializer->normalize($data, null, $context),
                $status
            );
        } catch (ExceptionInterface $e) {
            return $this->jsonException($e);
        }
    }

    protected function jsonNoContent(): JsonResponse
    {
        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
