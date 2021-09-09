<?php
/*
 * This file is part of the POSiFLORA Customer Shop API.
 *
 * (c) OrbitSoft LLC <support@orbitsoft.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Auth;

use App\Entity\Auth\User;
use Posiflora\CustomerApiClient\Model\SaaS\Customer;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Throwable;

class CachedUsersService implements UsersService
{
    const KEY_PREFIX = "user.";

    /**
     * @var UsersService
     */
    private $usersService;

    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;


    /**
     * @param ApiUsersService $usersService
     * @param CacheInterface $cache
     */
    public function __construct(ApiUsersService $usersService, CacheInterface $cache)
    {
        $this->usersService = $usersService;
        $this->cache = $cache;
    }

    /**
     * @param string $username
     * @param string $password
     * @return User|null
     * @throws Throwable
     * @throws InvalidArgumentException
     */
    public function get(string $username, string $password): ?User
    {
        $service = $this->usersService;
        $customer = $this->usersService->getActiveCustomer();
        /** @noinspection RegExpRedundantEscape */
        $usernameKey = preg_replace('/[\{\}\(\)\/\\\\@\:]/i', '|', $username);

        $cacheKey = sprintf(
            "%s_%s_%s_%s",
            self::KEY_PREFIX,
            $customer->getId(),
            $usernameKey,
            md5($password)
        );

        return $this->cache->get(
            $cacheKey,
            function(ItemInterface $item) use ($service, $username, $password) {
                $user = $service->get($username, $password);

                if (is_null($user)) {
                    $item->expiresAfter(1);
                }
                return $service->get($username, $password);
            }
        );
    }

    public function getActiveCustomer(): ?Customer
    {
        return $this->usersService->getActiveCustomer();
    }
}
