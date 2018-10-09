<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Common\Collections\Criteria;

/**
 * Class UserRepository.
 */
class UserRepository extends AbstractRepository
{
    /**
     * @param $email
     *
     * @return null|object|User
     */
    public function findByEmail($email)
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return \Doctrine\Common\Collections\Collection|User[]
     */
    public function findUsersSince(\DateTime $dateTime)
    {
        return $this->matching(Criteria::create()->where(Criteria::expr()->gte('creationTime', $dateTime)));
    }

    /**
     * @return array|User[]
     */
    public function findDeletedUsers()
    {
        return $this->findBy(['deleted' => true]);
    }
}
