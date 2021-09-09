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

use App\Entity\Auth\User;
use Reva2\JsonApi\Annotations\ApiResource;
use Reva2\JsonApi\Annotations\Attribute;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data transfer object that represent user
 *
 * @package App\Dto\Auth
 *
 * @ApiResource(name="users", loader="App\Loader\Auth\UserLoader:load")
 */
class UserDto
{
    /**
     * @var string|null
     * @Attribute(type="string")
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(min=2, max="24")
     * @Assert\Regex(pattern="/^[a-z]{1}[-a-z0-9_.@]+$/i")
     */
    public $username;

    /**
     * @var string|null
     * @Attribute(type="string")
     * @Assert\NotBlank(groups={"CreateCustomer"})
     * @Assert\Type(type="string")
     * @Assert\Length(min=6)
     */
    public $password;
}
