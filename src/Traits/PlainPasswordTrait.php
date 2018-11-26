<?php

namespace App\Traits;

/**
 * Trait PlainPasswordTrait.
 */
trait PlainPasswordTrait
{
    /**
     * @var string|null
     */
    private $plainPassword;

    /**
     * @return null|string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param null|string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }
}
