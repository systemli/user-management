<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @author doobry <doobry@systemli.org>
 */
class Voucher extends Constraint
{
    /**
     * @var bool|null
     */
    public $exists;

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        if (null !== $options && !\is_array($options)) {
            $options = array(
                'exists' => $options,
            );
        }

        parent::__construct($options);

        if (null === $this->exists) {
            throw new MissingOptionsException(sprintf('Option "exists" must be given for constraint %s', __CLASS__), array('min', 'max'));
        }
    }
}
