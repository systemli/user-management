<?php

namespace App\Handler;

use App\Entity\User;
use App\Exception\MultipleGpgKeysForUserException;
use App\Exception\NoGpgDataException;
use App\Exception\NoGpgKeyForUserException;
use Doctrine\Common\Persistence\ObjectManager;
use RuntimeException;
use Tuupola\Base32;

class OpenPGPWkdHandler
{
    /** @var ObjectManager */
    private $manager;

    /** @var GpgKeyHandler */
    private $keyHandler;

    /** @var string */
    private $wkdDirectory;

    /** @var string */
    private $wkdFormat;

    /**
     * OpenPGPWkdHandler constructor.
     */
    public function __construct(ObjectManager $manager,
                                GpgKeyHandler $keyHandler,
                                string $wkdDirectory,
                                string $wkdFormat)
    {
        $this->manager = $manager;
        $this->keyHandler = $keyHandler;
        $this->wkdDirectory = $wkdDirectory;
        $this->wkdFormat = $wkdFormat;
    }

    /**
     * @throws NoGpgDataException
     * @throws NoGpgKeyForUserException
     * @throws MultipleGpgKeysForUserException
     */
    public function importKey(User $user, string $key): ?string
    {
        $this->keyHandler->import($user->getEmail(), $key);
        $fingerprint = $this->keyHandler->getFingerprint();
        $sanitizedKey = $this->keyHandler->getKey();
        $this->keyHandler->tearDownGPGHome();

        $user->setWkdKey($sanitizedKey);
        $this->manager->flush();

        $this->exportKey($user);

        return $fingerprint;
    }

    public function getKeyFingerprint(User $user): ?string
    {
        $key = $user->getWkdKey();

        if (null === $key) {
            return null;
        }

        $this->keyHandler->import($user->getEmail(), $key);
        $fingerprint = $this->keyHandler->getFingerprint();
        $this->keyHandler->tearDownGPGHome();

        return $fingerprint;
    }

    public function deleteKey(User $user): void
    {
        $user->setWkdKey(null);
        $this->manager->flush();

        // TODO: Delete key from WKD directory!!!!
    }

    /**
     * Encodes the email address local part according to the WKD Web Wey Directory RFC draft.
     * See https://tools.ietf.org/html/draft-koch-openpgp-webkey-service-10 for further information.
     */
    private function wkdHash(string $localPart): string
    {
        $base32Encoder = new Base32(['characters' => Base32::ZBASE32]);

        return $base32Encoder->encode(sha1(strtolower($localPart)));
    }

    /**
     * @throws RuntimeException
     */
    public function exportKey(User $user): void
    {
        if (null === $wkdKey = $user->getWkdKey()) {
            return;
        }

        if ('advanced' === $this->wkdFormat) {
            $keyDir = $this->wkdDirectory.DIRECTORY_SEPARATOR.strtolower($user->getDomain()).DIRECTORY_SEPARATOR.'hu';
        } elseif ('simple' === $this->wkdFormat) {
            $keyDir = $this->wkdDirectory.DIRECTORY_SEPARATOR.'hu';
        } else {
            throw new RuntimeException(sprintf('Error: unsupported WKD format: %s', $this->wkdFormat));
        }

        if (!is_dir($keyDir) && !mkdir($concurrentDirectory = $keyDir, 0775, true) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $localPart = explode('@', $user->getEmail())[0];
        $wkdHash = $this->wkdHash($localPart);

        file_put_contents($keyDir.DIRECTORY_SEPARATOR.$wkdHash, $wkdKey);
    }
}