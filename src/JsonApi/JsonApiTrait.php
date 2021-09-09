<?php
/*
 * This file is part of the Orbit Ad Serving: API.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\JsonApi;

use Exception;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Contracts\Http\ResponsesInterface;
use Neomerx\JsonApi\Document\Error;
use Neomerx\JsonApi\Exceptions\ErrorCollection;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Ramsey\Uuid\Uuid;
use Reva2\JsonApi\Contracts\Http\RequestInterface;
use Reva2\JsonApi\Contracts\Services\EnvironmentInterface;
use Reva2\JsonApi\Contracts\Services\JsonApiServiceInterface;
use Reva2\JsonApi\Http\Query\ListQueryParameters;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait that add access to JSON API service
 *
 * @package Orbit\RestApi\JsonApi
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
trait JsonApiTrait
{
    /**
     * @var JsonApiServiceInterface
     */
    protected JsonApiServiceInterface $jsonApiService;

    /**
     * Returns JSON API service
     *
     * @return JsonApiServiceInterface
     * @throws RuntimeException
     */
    public function getJsonApiService(): JsonApiServiceInterface
    {
        if (null === $this->jsonApiService) {
            throw new RuntimeException('JSON API service not specified');
        }

        return $this->jsonApiService;
    }

    /**
     * Sets JSON API service
     *
     * @param JsonApiServiceInterface $jsonApiService
     * @return $this
     */
    public function setJsonApiService(JsonApiServiceInterface $jsonApiService): self
    {
        $this->jsonApiService = $jsonApiService;

        return $this;
    }

    /**
     * Create response with regular JSON API Document in body
     *
     * @param RequestInterface $request
     * @param object|array $data
     * @param mixed|null $meta
     * @param int $code
     * @param array|null $links
     * @param array $headers
     * @return Response
     */
    protected function getContentResponse(
        RequestInterface $request,
        $data,
        $meta = null,
        $code = Response::HTTP_OK,
        array $links = null,
        array $headers = []
    ): Response {
        return $this
            ->getResponseFactory($request)
            ->getContentResponse($data, $code, $links, $meta, $headers);
    }

    /**
     * Get response for newly created resource with HTTP code 201 (adds 'location' header).
     *
     * @param RequestInterface $request
     * @param object $resource
     * @param mixed|null $meta
     * @param array|null $links
     * @param array $headers
     * @return Response
     */
    protected function getCreatedResponse(
        RequestInterface $request,
        $resource,
        $meta = null,
        $links = null,
        array $headers = []
    ): Response {
        return $this->getResponseFactory($request)->getCreatedResponse($resource, $links, $meta, $headers);
    }

    /**
     * Get response with HTTP code only.
     *
     * @param RequestInterface $request
     * @param int $code
     * @param array $headers
     * @return Response
     */
    protected function getEmptyResponse(RequestInterface $request, $code, array $headers = []): Response
    {
        return $this->getResponseFactory($request)->getCodeResponse($code, $headers);
    }

    /**
     * Get response with only resource identifiers.
     *
     * @param RequestInterface $request
     * @param object|array $data
     * @param mixed|null   $meta
     * @param int          $code
     * @param array|null   $links
     * @param array        $headers
     * @return Response
     */
    protected function getIdentifiersResponse(
        RequestInterface $request,
        $data,
        $meta = null,
        $code = Response::HTTP_OK,
        $links = null,
        array $headers = []
    ) {
        return $this->getResponseFactory($request)->getIdentifiersResponse($data, $code, $links, $meta, $headers);
    }

    /**
     * Get response with meta information only.
     *
     * @param RequestInterface $request
     * @param array|object $meta
     * @param int $code
     * @param array $headers
     * @return Response
     */
    protected function getMetaResponse(
        RequestInterface $request,
        $meta,
        $code = Response::HTTP_OK,
        array $headers = []
    ): Response {
        return $this->getResponseFactory($request)->getMetaResponse($meta, $code, $headers);
    }

    /**
     * Get response with JSON API Error in body.
     *
     * @param RequestInterface $request
     * @param ErrorInterface|ErrorInterface[]|ErrorCollection $errors
     * @param int $code
     * @param array $headers
     * @return Response
     */
    protected function getErrorResponse(
        RequestInterface $request,
        $errors,
        $code = Response::HTTP_BAD_REQUEST,
        array $headers = []
    ) {
        return $this->getResponseFactory($request)->getErrorResponse($errors, $code, $headers);
    }

    /**
     * Returns response factory for specified request
     *
     * @param RequestInterface $request
     * @return ResponsesInterface
     */
    protected function getResponseFactory(RequestInterface $request): ResponsesInterface
    {
        return $this->getJsonApiService()->getResponseFactory($request);
    }

    /**
     * Parse JSON API request
     *
     * @param Request $request
     * @param EnvironmentInterface|null $environment
     * @return RequestInterface
     */
    protected function parseJsonApiRequest(Request $request, EnvironmentInterface $environment = null): RequestInterface
    {
        return $this->getJsonApiService()->parseRequest($request, $environment);
    }

    /**
     * Returns metadata for list response
     *
     * @param ListQueryParameters $params
     * @return array
     */
    protected function getCommonListResponseMetadata(ListQueryParameters $params): array
    {
        return [
            'page' => $params->getPaginationParameters()
        ];
    }

    /**
     * Creates JSON API error
     *
     * @param string $status
     * @param string $code
     * @param string $title
     * @param string|null $details
     * @param array|null $source
     * @return ErrorInterface
     * @throws Exception
     */
    protected function createJsonApiError(
        string $status,
        string $code,
        string $title,
        ?string $details = null,
        ?array $source = null
    ): ErrorInterface {
        return new Error(
            Uuid::uuid4()->toString(),
            null,
            $status,
            $code,
            $title,
            $details,
            $source
        );
    }

    /**
     * Creates bad request exception
     *
     * @param string|null $details
     * @param array|null $source
     * @return JsonApiException
     * @throws Exception
     */
    protected function createBadRequestException(?string $details = null, ?array $source = null): JsonApiException
    {
        $error = $this->createJsonApiError((string) Response::HTTP_BAD_REQUEST,
            '8029b35a-15b3-44ac-9707-6fe2f2f50aae',
            'Bad resource',
            $details,
            $source
        );

        return new JsonApiException($error, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Creates JSON API resource not found exception
     *
     * @param string|null $details
     * @param array|null $source
     * @return JsonApiException
     * @throws Exception
     */
    protected function createNotFoundException(?string $details = null, ?array $source = null): JsonApiException
    {
        $error = $this->createJsonApiError(
            (string) Response::HTTP_NOT_FOUND,
            '690cdebd-d33a-4aa6-aca3-759d33d499d4',
            'Resource not found',
            $details,
            $source
        );

        return new JsonApiException($error, Response::HTTP_NOT_FOUND);
    }

    /**
     * Creates access denied exception
     *
     * @param string|null $details
     * @param array|null $source
     * @return JsonApiException
     * @throws Exception
     */
    protected function createAccessDeniedException(?string $details = null, ?array $source = null): JsonApiException
    {
        $error = $this->createJsonApiError(
            (string) Response::HTTP_FORBIDDEN,
            'e5573881-e532-4657-beae-bc6f8c0d7658',
            'Access denied',
            $details,
            $source
        );

        return new JsonApiException($error, Response::HTTP_FORBIDDEN);
    }

    /**
     * Create conflict exception
     *
     * @param string|null $details
     * @param array|null $source
     * @return JsonApiException
     * @throws Exception
     */
    protected function createConflictException(?string $details = null, ?array $source = null): JsonApiException
    {
        $error = $this->createJsonApiError(
            (string) Response::HTTP_CONFLICT,
            '32351f01-58c3-49b2-b1b8-217103e40b58',
            'Conflict with the current state of the target resource',
            $details,
            $source
        );

        return new JsonApiException($error, Response::HTTP_CONFLICT);
    }
}
