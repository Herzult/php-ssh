<?php declare(strict_types=1);
/**
 * @author    Axel Helmert <ah@luka.de>
 * @license   MIT
 * @copyright Copyright (c) 2018 LUKA netconsult GmbH (www.luka.de)
 */

namespace SshTest\FunctionalTests;

use function assertInternalType;
use function fclose;
use function feof;
use function fread;
use function fwrite;
use function get_resource_type;
use PHPUnit\Framework\TestCase;
use function socket_close;
use function stream_get_contents;
use function stream_select;

class TunnelTest extends TestCase
{
    use ProvideSessionTrait;

    public function testCreatesTunnelledStream() : void
    {
        $session = $this->createSession();
        $resource = $session->getTunnel()->create('www.luka.de', 80);

        self::assertInternalType('resource', $resource);
        self::assertSame('stream', get_resource_type($resource));

        fclose($resource);
    }

    public function testCreatedStreamAllowsSocketCommunication() : void
    {
        $session = $this->createSession();
        $resource = $session->getTunnel()->create('www.luka.de', 80);
        $request = "GET / HTTP/1.1\r\n"
                 . "Host: www.luka.de\r\n"
                 . "User-Agent: PHPUnit SSHTunnelTest\r\n"
                 . "Connection: Close\r\n\r\n";

        // This will send an HTTP-Request and expects an HTTP-Response

        fwrite($resource, $request);
        $data = '';
        $timeout = time() + 10;

        while (!feof($resource)) {
            $data .= fread($resource, 1024);

            if (time() > $timeout) {
                self::fail('Timeout');
            }
        }

        self::assertStringStartsWith('HTTP/', $data);
        fclose($resource);
    }
}
