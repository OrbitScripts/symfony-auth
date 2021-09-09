<?php
/*
 * This file is part of the POSiFLORA Customer Shop API.
 *
 * (c) OrbitSoft LLC <support@orbitsoft.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Exceptions\ErrorCollection;
use Reva2\JsonApi\Contracts\Http\RequestInterface;
use Reva2\JsonApi\Contracts\Services\JsonApiServiceInterface;
use Reva2\JsonApi\Http\Query\ListQueryParameters;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base class for JSON API controllers
 *
 * @package App\Controller
 */
abstract class JsonApiController
{
    /**
     * @var JsonApiServiceInterface
     */
    protected JsonApiServiceInterface $jsonApiService;

    /**
     * Constructor
     *
     * @param JsonApiServiceInterface $jsonApiService
     */
    public function __construct(JsonApiServiceInterface $jsonApiService)
    {
        $this->jsonApiService = $jsonApiService;
    }

    /**
     * Creates response with regular JSON API document in body
     *
     * @param RequestInterface $request
     * @param mixed $data
     * @param mixed|null $metadata
     * @param int $code
     * @param array $links
     * @param array $headers
     * @return Response
     */
    protected function buildContentResponse(
        RequestInterface $request,
        $data,
        $metadata = null,
        $code = Response::HTTP_OK,
        array $links = [],
        array $headers = []
    ): Response {
        return $this->jsonApiService
            ->getResponseFactory($request)
            ->getContentResponse($data, $code, $links, $metadata, $headers);
    }

    /**
     * Creates response for newly created resource (with HTTP code 201 and 'location' header).
     *
     * @param RequestInterface $request
     * @param mixed $resource
     * @param mixed|null $metadata
     * @param array|null $links
     * @param array $headers
     * @return Response
     */
    protected function buildCreatedResponse(
        RequestInterface $request,
        $resource,
        $metadata = null,
        array $links = null,
        array $headers = []
    ): Response {
        return $this->jsonApiService
            ->getResponseFactory($request)
            ->getCreatedResponse($resource, $links, $metadata, $headers);
    }

    /**
     * Creates empty response
     *
     * @param RequestInterface $request
     * @param int $code
     * @param array $headers
     * @return Response
     */
    protected function buildEmptyResponse(
        RequestInterface $request,
        $code = Response::HTTP_NO_CONTENT,
        array $headers = []
    ): Response {
        return $this->jsonApiService->getResponseFactory($request)->getCodeResponse($code, $headers);
    }

    /**
     * Creates response with JSON API Error in body.
     *
     * @param RequestInterface $request
     * @param ErrorInterface|ErrorInterface[]|ErrorCollection $errors
     * @param int $code
     * @param array $headers
     * @return Response
     */
    protected function buildErrorResponse(
        RequestInterface $request,
        $errors,
        $code = Response::HTTP_BAD_REQUEST,
        array $headers = []
    ): Response {
        return $this->jsonApiService->getResponseFactory($request)->getErrorResponse($errors, $code, $headers);
    }

    /**
     * Create response with `not found` JSON API Error in body.
     *
     * @param RequestInterface $request
     * @param ErrorInterface|ErrorInterface[]|ErrorCollection $errors
     * @param int $code
     * @param array $headers
     * @return Response
     */
    protected function buildNotFoundErrorResponse(
        RequestInterface $request,
        $errors,
        $code = Response::HTTP_NOT_FOUND,
        array $headers = []
    ): Response {
        return $this->jsonApiService->getResponseFactory($request)->getErrorResponse($errors, $code, $headers);
    }

    /**
     * Returns metadata for list response
     *
     * @param ListQueryParameters $parameters
     * @return array
     */
    protected function getListResponseMetadata(ListQueryParameters $parameters): array
    {
        return ['page' => $parameters->getPaginationParameters()];
    }
}
