<?php

namespace App\Creator;

use App\Exception\ValidationException;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AbstractCreator.
 */
abstract class AbstractCreator
{
    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * AbstractCreator constructor.
     *
     * @param ObjectManager            $manager
     * @param ValidatorInterface       $validator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ObjectManager $manager, ValidatorInterface $validator, EventDispatcherInterface $eventDispatcher)
    {
        $this->manager = $manager;
        $this->validator = $validator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $entity
     * @param array|null $validationGroups
     *
     * @throws ValidationException
     */
    protected function validate($entity, array $validationGroups = null): void
    {
        $violations = $this->validator->validate($entity, null, $validationGroups);

        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }
    }

    /**
     * @param $entity
     */
    protected function save($entity): void
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
