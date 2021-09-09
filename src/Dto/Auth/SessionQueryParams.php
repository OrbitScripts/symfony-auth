<?php
/*
 * This file is part of the Posiflora Billing API.
 *
 * (c) OrbitSoft LLC <support@orbitsoft.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Dto\Auth;

use Reva2\JsonApi\Http\Query\QueryParameters;

/**
 * DTO which represent query parameters for endpoints that returns
 * information about single auth session
 *
 * @package App\Dto\Auth
 */
class SessionQueryParams extends QueryParameters
{
    /**
     * @return array|string[]
     */
    protected function getAllowedIncludePaths()
    {
        return ['user'];
    }
}
