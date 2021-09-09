<?php
/*
 * This file is part of the Posiflora Billing API.
 *
 * (c) OrbitSoft LLC <support@orbitsoft.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Schema\Auth;

use App\Entity\Auth\User;
use Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * JSON API schema for resources that represent users
 *
 * @package App\Schema\Auth
 */
class UserSchema extends SchemaProvider
{
    protected $resourceType = 'users';

    /**
     * @param User $resource
     * @return string
     */
    public function getId($resource): string
    {
        return (string) $resource->getId();
    }

    /**
     * @param User $resource
     * @return array
     */
    public function getAttributes($resource): array
    {
        return [
            'username' => $resource->getUsername(),
            'firstName' => $resource->getFirstName(),
            'lastName' => $resource->getLastName(),
            'currency' => $resource->getCurrency()
        ];
    }
}
