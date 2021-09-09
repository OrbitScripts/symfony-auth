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
use App\Mappers\Auth\UserMapper;
use App\Service\SaaS\CustomerService;
use Posiflora\CustomerApiClient\Client;
use Posiflora\CustomerApiClient\Model\SaaS\Customer as SaasCustomer;
use Posiflora\CustomerApiClient\Model\Staff\WorkersListQueryParams;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Throwable;

class ApiUsersService implements UsersService
{
    /**
     * @var Client
     */
    private Client $apiClient;

    /**
     * @var CustomerService
     */
    private CustomerService $saasService;

    /**
     * @var UserMapper
     */
    private UserMapper $userMapper;

    /**
     * @param Client $apiClient
     * @param CustomerService $saasService
     * @param UserMapper $userMapper
     */
    public function __construct(Client $apiClient, CustomerService $saasService, UserMapper $userMapper)
    {
        $this->apiClient = $apiClient;
        $this->saasService = $saasService;
        $this->userMapper = $userMapper;
    }

    /**
     * @param string $username
     * @param string $password
     * @return User|null
     * @throws BadCredentialsException
     * @throws Throwable
     */
    public function get(string $username, string $password): ?User
    {
        $customer = $this->getActiveCustomer();
        $transport = $this->apiClient->customerTransport($customer);

        $params = (new WorkersListQueryParams())
            ->setUserLogin($username)
            ->setUserPassword($password)
            ->setIncludePaths(['user'])
            ->setPageSize(1);
        $response = $transport->workers()->getList($params);

        if (count($response->data) === 0) {
            return null;
        }

        $saasInfo = $transport->saasInfo()->get();

        return $this->userMapper->apiWorkerToEntity($response->data[0], $saasInfo->data->currencyUnicode);
    }

    /**
     * @return SaasCustomer|null
     */
    public function getActiveCustomer(): ?SaasCustomer
    {
        return $this->saasService->getActiveCustomer();
    }
}
