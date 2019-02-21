<?php

namespace App\Command;

use App\Handler\UserAuthenticationHandler;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QuotaCommand extends Command
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var UserAuthenticationHandler
     */
    private $handler;

    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(ObjectManager $manager, UserAuthenticationHandler $handler)
    {
        $this->manager = $manager;
        $this->handler = $handler;
        $this->repository = $this->manager->getRepository('App:User');
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:users:quota')
            ->setDescription('Get quota of user if set')
            ->addArgument(
                'email',
                InputOption::VALUE_REQUIRED,
                'email to get quota of');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // parse arguments
        $email = $input->getArgument('email');

        // Check if user exists
        $user = $this->repository->findByEmail($email);

        if (null === $user) {
            return 1;
        }

        // get quota
        $quota = $user->getQuota();

        if (null === $quota) {
            return 0;
        }

        $output->writeln(sprintf('%u', $quota));
    }
}
