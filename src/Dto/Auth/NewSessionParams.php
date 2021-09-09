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

use Reva2\JsonApi\Annotations\ApiResource;
use Reva2\JsonApi\Annotations\Attribute;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Parameters for new auth session
 *
 * @package App\Dto\Auth
 *
 * @ApiResource(name="sessions")
 */
class NewSessionParams
{
    /**
     * @var string|null
     * @Attribute(type="string")
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     */
    public $username;

    /**
     * @var string|null
     * @Attribute(type="string")
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     */
    public $password;
}
