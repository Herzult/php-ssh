<?php declare(strict_types=1);
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   MIT
 * @copyright Copyright (c) 2023 LUKA netconsult GmbH (www.luka.de)
 */

namespace SshTest\Authentication;

use Ssh\Authentication\KeyPair;
use PHPUnit\Framework\TestCase;

class KeyPairTest extends TestCase
{
    public function testBuildThePublicKeyFileFromPrivateKey(): void
    {
        $pair = new KeyPair('/some/dir/id_ecdsa');

        self::assertSame('/some/dir/id_ecdsa', $pair->privateKeyFile);
        self::assertSame('/some/dir/id_ecdsa.pub', $pair->publicKeyFile);
    }

    public function testUseCustomPublicKeyFile(): void
    {
        $pair = new KeyPair('/some/dir/id_ecdsa', '/some/other/pub_key');

        self::assertSame('/some/dir/id_ecdsa', $pair->privateKeyFile);
        self::assertSame('/some/other/pub_key', $pair->publicKeyFile);
    }

    public function testShouldCheckExistence(): void
    {
        $existing = new KeyPair(__DIR__ . '/assets/fake-keys/private.key', __DIR__ . '/assets/fake-keys/public.key');
        $pubMissing = new KeyPair(__DIR__ . '/assets/fake-keys/private.key', __DIR__ . '/assets/fake-keys/missing.key');
        $privateMissing = new KeyPair(__DIR__ . '/assets/fake-keys/missing.key', __DIR__ . '/assets/fake-keys/public.key');
        $bothMissing = new KeyPair(__DIR__ . '/assets/fake-keys/missing.key', __DIR__ . '/assets/fake-keys/missing.pub');

        self::assertTrue($existing->exists());
        self::assertFalse($pubMissing->exists());
        self::assertFalse($privateMissing->exists());
        self::assertFalse($bothMissing->exists());
    }
}
