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


use App\Entity\Auth\User;
use Posiflora\CustomerApiClient\Model\SaaS\Customer as SaasCustomer;

interface UsersService
{
    /**
     * @param string $username
     * @param string $password
     * @return User|null
     */
    public function get(string $username, string $password): ?User;

    /**
     * @return SaasCustomer|null
     */
    public function getActiveCustomer(): ?SaasCustomer;
}
