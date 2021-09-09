<?php
/*
 * This file is part of the Posiflora Billing API.
 *
 * (c) OrbitSoft LLC <support@orbitsoft.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Auth;

use App\Dto\Auth\NewSessionParams;
use App\Entity\Auth\Session;
use App\Repository\Auth\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

/**
 * Service for auth sessions management
 *
 * @package App\Service\Auth
 */
class SessionsService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var JWTTokenManagerInterface
     */
    private JWTTokenManagerInterface $jwtManager;

    /**
     * @var UserCheckerInterface
     */
    private UserCheckerInterface $checker;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var UsersService
     */
    private UsersService $usersService;

    /**
     * @param EntityManagerInterface $em
     * @param JWTTokenManagerInterface $jwtManager
     * @param UserCheckerInterface $checker
     * @param UserRepository $userRepository
     * @param UsersService $usersService
     */
    public function __construct(
        EntityManagerInterface $em,
        JWTTokenManagerInterface $jwtManager,
        UserCheckerInterface $checker,
        UserRepository $userRepository,
        UsersService $usersService
    ) {
        $this->em = $em;
        $this->jwtManager = $jwtManager;
        $this->checker = $checker;
        $this->userRepository = $userRepository;
        $this->usersService = $usersService;
    }

    /**
     * Creates new auth session
     *
     * @param Request $request
     * @param NewSessionParams $params
     * @return Session|null
     * @throws Exception
     */
    public function create(Request $request, NewSessionParams $params): ?Session
    {
        $user = $this->usersService->get($params->username, $params->password);

        if (is_null($user)) {
            return null;
        }

        $dbUser = $this->userRepository->findOneBy(['username' => $user->getUsername()]);
        if (!is_null($dbUser)) {
            $this->em->remove($dbUser);
            $this->em->flush();
        }

        $this->em->persist($user);
        $this->em->flush();

        $this->checker->checkPreAuth($user);
        $this->checker->checkPostAuth($user);

        $session = new Session();
        $session
            ->setUser($user)
            ->setAccessToken($this->jwtManager->create($user))
            ->setRefreshToken(bin2hex(openssl_random_pseudo_bytes(64)))
            ->setIp($request->getClientIp())
            ->setUserAgent($request->headers->get('User-Agent', 'Unknown'));

        return $session;
    }
}
