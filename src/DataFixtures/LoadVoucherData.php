<?php

namespace App\DataFixtures;

use App\Entity\Voucher;
use App\Factory\VoucherFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadVoucherData extends Fixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $users = $manager->getRepository('App:User')->findAll();

        for ($i = 0; $i < 1000; ++$i) {
            /** @var Voucher $voucher */
            $voucher = VoucherFactory::create($users[mt_rand(1, count($users) - 1)]);

            $invitedUser = $users[mt_rand(0, count($users) - 1)];
            $voucher->setInvitedUser($invitedUser);
            $voucher->setRedeemedTime(new \DateTime());

            $invitedUser->setInvitationVoucher($voucher);

            if (mt_rand(0, 100) > 50) {
                $voucher->setRedeemedTime(new \DateTime(sprintf('-%d days', mt_rand(1, 100))));
            }

            $manager->persist($voucher);
        }

        // add redeemed voucher to a suspicious parent
        $user = $manager->getRepository('App:User')->findByEmail('suspicious@example.org');
        $voucher = VoucherFactory::create($user);
        $invitedUser = $users[mt_rand(0, count($users) - 1)];
        $voucher->setInvitedUser($invitedUser);
        $voucher->setRedeemedTime(new \DateTime(sprintf('-%d days', 100)));
        $invitedUser->setInvitationVoucher($voucher);
        $manager->persist($voucher);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 3;
    }
}
