<?php
/*
 * This file is part of the Posiflora Billing API.
 *
 * (c) OrbitSoft LLC <support@orbitsoft.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Auth;

use App\Controller\JsonApiController;
use App\JsonApi\JsonApiTrait;
use App\Service\Auth\SessionsService;
use Exception;
use Ramsey\Uuid\Uuid;
use Reva2\JsonApi\Annotations\ApiRequest;
use Reva2\JsonApi\Contracts\Services\JsonApiServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Security;

/**
 * Auth sessions API controller
 *
 * @author Sergey Revenko <sergey.revenko@orbitsoft.com>
 * @package App\Controller\Auth
 */
class SessionsController extends JsonApiController
{
    use JsonApiTrait;

    /**
     * @var SessionsService
     */
    private SessionsService $sessions;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @param JsonApiServiceInterface $jsonApiService
     * @param Security $security
     * @param SessionsService $sessions
     */
    public function __construct(JsonApiServiceInterface $jsonApiService, Security $security, SessionsService $sessions)
    {
        parent::__construct($jsonApiService);

        $this->security = $security;
        $this->sessions = $sessions;
    }

    /**
     * Creates new session
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     *
     * @Route("/v1/sessions", methods={"POST"}, name="sessions.create")
     * @ApiRequest(query="App\Dto\Auth\SessionQueryParams", body="App\Dto\Auth\NewSessionDocument")
     */
    public function createAction(Request $request): Response
    {
        $apiRequest = $this->jsonApiService->parseRequest($request);
        $doc = $apiRequest->getBody();

        try {
            $session = $this->sessions->create($request, $doc->data);

            if (empty($session)) {
                $error = $this->createJsonApiError(
                    (string) Response::HTTP_UNAUTHORIZED,
                    'd5211770-5728-4fa1-96f9-9cc41adf4bab',
                    'Bad credentials',
                    'Invalid username or password'
                );

                return $this->buildErrorResponse($apiRequest, $error, Response::HTTP_UNAUTHORIZED);
            }
        } catch (AccountStatusException $e) {
            $error = $this->createJsonApiError(
                (string) Response::HTTP_UNAUTHORIZED,
                '772cb0b5-af04-4306-9492-fde155d75418',
                'Unauthorized',
                $e->getMessage()
            );

            return $this->buildErrorResponse($apiRequest, $error, Response::HTTP_UNAUTHORIZED);
        }

        return $this->buildCreatedResponse($apiRequest, $session);
    }
}
