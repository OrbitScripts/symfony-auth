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

use App\Entity\Auth\Session;
use Neomerx\JsonApi\Schema\SchemaProvider;

/**
 * JSON API schema for user sessions
 *
 * @package App\Schema\Auth
 */
class SessionSchema extends SchemaProvider
{
    protected $resourceType = 'sessions';

    /**
     * @param Session $resource
     * @return string
     */
    public function getId($resource): string
    {
        return $resource->getId();
    }

    /**
     * @param object|Session $resource
     * @return array
     */
    public function getAttributes($resource): array
    {
        $attributes =[
            'refreshToken' => $resource->getRefreshToken(),
            'ip' => $resource->getIp(),
            'userAgent' => $resource->getUserAgent()
        ];

        if (!empty($resource->getAccessToken())) {
            $attributes['accessToken'] = $resource->getAccessToken();
        }

        return $attributes;
    }

    /**
     * @param object|Session $resource
     * @param bool $isPrimary
     * @param array $includeRelationships
     * @return array
     */
    public function getRelationships($resource, $isPrimary, array $includeRelationships): array
    {
        return [
            'user' => [self::DATA => $resource->getUser()]
        ];
    }
}
