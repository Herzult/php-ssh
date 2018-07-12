<?php declare(strict_types=1);
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   MIT
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace SshTest\OpenSSH;

use Ssh\Configuration;
use Ssh\HostConfiguration;
use Ssh\OpenSSH\ConfigFile;
use PHPUnit\Framework\TestCase;

class ConfigFileTest extends TestCase
{
    const VALID_FILE_FIXTURE = __DIR__ . '/../Fixtures/config_valid';

    public function testClassConstructsWithValidConfig()
    {
        $instance = new ConfigFile(new HostConfiguration('my-host'), self::VALID_FILE_FIXTURE);
        self::assertInstanceOf(Configuration::class, $instance);
    }
}
