<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Voucher;
use Doctrine\Common\Collections\Criteria;

/**
 * Class VoucherRepository.
 */
class VoucherRepository extends AbstractRepository
{
    /**
     * @param $code
     *
     * @return null|Voucher|object
     */
    public function findByCode($code)
    {
        return $this->findOneBy(['code' => $code]);
    }

    /**
     * @return int
     */
    public function countRedeemedVouchers()
    {
        return $this->matching(Criteria::create()->where(Criteria::expr()->neq('redeemedTime', null)))->count();
    }

    /**
     * @param User $user
     *
     * @return array|Voucher[]
     */
    public function findByUser(User $user)
    {
        return $this->findBy(['user' => $user]);
    }

    /**
     * @return Voucher[]|array
     */
    public function getOldVouchers()
    {
        return $this->createQueryBuilder('voucher')
            ->join('voucher.invitedUser', 'invitedUser')
            ->where('voucher.redeemedTime < :date')
            ->setParameter('date', new \DateTime('-3 months'))
            ->orderBy('voucher.redeemedTime')
            ->getQuery()
            ->getResult();
    }
}
