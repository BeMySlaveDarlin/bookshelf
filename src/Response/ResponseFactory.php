<?php

declare(strict_types=1);

namespace App\Response;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseFactory
{
    public const JSON_OPTIONS = \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES;

    private ?string $lang = null;

    public function setLang(?string $lang = null): ResponseFactory
    {
        $this->lang = $lang;

        return $this;
    }

    public function createSuccessResponse(array $items, ?string $message = null): JsonResponse
    {
        $data = $this->createResponseData(debug_backtrace()[1]['function']);
        $data['data'] = $items;
        $data['message'] = $message ?? 'Ok';

        return $this->createResponseObject($data, Response::HTTP_OK);
    }

    public function createErrorResponse(\Throwable $throwable): JsonResponse
    {
        $data = $this->createResponseData(debug_backtrace()[1]['function']);
        $data['error'] = $throwable->getMessage();

        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        if ($throwable instanceof EntityNotFoundException) {
            $status = Response::HTTP_NOT_FOUND;
        }
        if (\is_subclass_of($throwable, Exception::class)) {
            $status = Response::HTTP_BAD_REQUEST;
        }

        return $this->createResponseObject($data, $status);
    }

    private function createResponseData(string $action): array
    {
        $responseData = [];
        if (null !== $this->lang) {
            $responseData['lang'] = $this->lang;
        }
        $responseData['action'] = $action;

        return $responseData;
    }

    private function createResponseObject(array $data, int $status): JsonResponse
    {
        $response = new JsonResponse($data, $status);
        $response->setEncodingOptions(JsonResponse::DEFAULT_ENCODING_OPTIONS | self::JSON_OPTIONS);

        return $response;
    }
}
