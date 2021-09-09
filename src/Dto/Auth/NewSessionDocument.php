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

use Reva2\JsonApi\Annotations\ApiDocument;
use Reva2\JsonApi\Annotations\Content;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * JSON API document that contains parameters for new session
 *
 * @package App\Dto\Auth
 *
 * @ApiDocument()
 */
class NewSessionDocument
{
    /**
     * @var NewSessionParams
     * @Content(type="App\Dto\Auth\NewSessionParams")
     * @Assert\NotBlank()
     * @Assert\Type(type="App\Dto\Auth\NewSessionParams")
     * @Assert\Valid()
     */
    public $data;
}
