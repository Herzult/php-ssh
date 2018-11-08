<?php declare(strict_types=1);
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   MIT
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace SshTest\FunctionalTests;

use Ssh\Authentication\Password;
use Ssh\HostConfiguration;
use Ssh\Session;

trait ProvideSessionTrait
{
    public function createSession() : Session
    {
        return new Session(
            new HostConfiguration(
                TEST_HOST,
                null,
                [],
                [],
                null
            ),
            new Password(TEST_USER, TEST_PASSWORD)
        );
    }
}
