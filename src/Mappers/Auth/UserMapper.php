<?php


namespace App\Mappers\Auth;

use App\Entity\Auth\User;
use Posiflora\CustomerApiClient\Model\Staff\Worker;


class UserMapper
{
    /**
     * @param Worker $worker
     * @param string $currency
     * @return User
     */
    public function apiWorkerToEntity(Worker $worker, string $currency): User
    {
        return (new User)
            ->setId($worker->id)
            ->setUsername($worker->user->login)
            ->setFirstname($worker->firstName)
            ->setLastName($worker->lastName)
            ->setCurrency($currency);
    }
}
