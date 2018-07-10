<?php


namespace Ssh\FunctionalTests;


use Ssh\Authentication\Password;
use Ssh\Configuration;
use Ssh\Session;
use Ssh\Sftp;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Julius Beckmann
 *
 * @group functional
 *
 * @covers \Ssh\Sftp
 */
class SftpTest extends \PHPUnit_Framework_TestCase
{
    /** @var Configuration */
    private $configuration;

    /** @var  Password */
    private $auth;

    /** @var  Session */
    private $session;

    /** @var  Sftp */
    private $sftp;

    /** @var  string */
    private $tmpDir;

    protected function setUp()
    {
        $this->configuration = new Configuration('localhost');
        $this->auth = new Password(TEST_USER, TEST_PASSWORD);
        $this->session = new Session($this->configuration, $this->auth);

        $this->sftp = $this->session->getSftp();

        $this->createFixtures();
    }

    protected function createFixtures()
    {
        $this->tmpDir = rtrim(sys_get_temp_dir(), '/') . '/' . 'HerzultPHPSSH';

        $fs = new Filesystem();

        $fs->remove($this->tmpDir);
        $fs->mkdir($this->tmpDir);

        $fs->touch($this->tmpDir . '/foo.txt');

        $fs->mkdir($this->tmpDir . '/bar');
        $fs->mkdir($this->tmpDir . '/bar/alice');
        $fs->dumpFile($this->tmpDir . '/bar/bob.txt', 'SomeContent');

        $fs->chmod($this->tmpDir, 0777, 0000, true);
    }

    protected function createLargeDirectoryTree()
    {
        $basePath = $this->tmpDir . '/tree';
        $fs = new Filesystem();

        // Create a larger directory tree
        foreach (range('a', 'c') as $a) {
            $fs->mkdir($basePath . '/' . $a);
            foreach (range('a', 'c') as $b) {
                $fs->mkdir($basePath . '/' . $a . '/' . $b);
                foreach (range('a', 'c') as $c) {
                    $fs->mkdir($basePath . '/' . $a . '/' . $b . '/' . $c);
                    foreach (range('1', '3') as $file) {
                        $fs->touch($basePath . '/' . $a . '/' . $b . '/' . $c . '/' . $file . '.txt');
                    }
                }
            }
        }
        $fs->chmod($basePath, 0777, 0000, true);
    }

    public function testSymlinks()
    {
        $path = $this->tmpDir . '/symlink';
        $pathTarget = $this->tmpDir . '/bar/bob.txt';

        $this->assertTrue($this->sftp->symlink($pathTarget, $path));
        $this->assertEquals($pathTarget, $this->sftp->readlink($path));
        $this->assertEquals(
          array(
            7,
            'size',
            4,
            'uid',
            5,
            'gid',
            2,
            'mode',
            8,
            'atime',
            9,
            'mtime'
          ),
          array_keys($this->sftp->lstat($path))
        );
    }

    public function testListlargeDirectory()
    {
        $this->createLargeDirectoryTree();

        $list = $this->sftp->listDirectory($this->tmpDir . '/tree', true);

        $this->assertCount(81, $list['files']);
        $this->assertCount(39, $list['directories']);
    }

    public function testReadFile()
    {
        $this->assertEquals('SomeContent', $this->sftp->read($this->tmpDir . '/bar/bob.txt'));

        $this->assertEquals(false, $this->sftp->read($this->tmpDir . '/not_existing.txt'));
    }

    public function testWriteAndReadFile()
    {
        $path = $this->tmpDir . '/new.txt';

        // Creating new file.
        $this->assertEquals(true, $this->sftp->write($path, 'MyContent'));
        $this->assertEquals('MyContent', $this->sftp->read($path));

        // Overwriting.
        $this->assertEquals(true, $this->sftp->write($path, 'AnotherContent'));
        $this->assertEquals('AnotherContent', $this->sftp->read($path));
    }

    public function testRealpath()
    {
        // Will work.
        $this->assertEquals('/etc/hosts', $this->sftp->realpath('/usr//../../../../etc//hosts'));

        // This should not work.
        $this->assertEquals(false, $this->sftp->realpath('/foo/bar//../../../../etc//hosts'));
    }

    public function testUnlinkFile()
    {
        $path = $this->tmpDir . '/bar/bob.txt';

        $this->assertTrue(is_array($this->sftp->stat($path)));

        $this->assertTrue($this->sftp->unlink($path));

        $this->assertFalse($this->sftp->stat($path));

        // Unlinking a second time should not work.
        $this->assertFalse($this->sftp->unlink($path));
    }

    public function testRenameFile()
    {
        $path = $this->tmpDir . '/bar/bob.txt';
        $pathTarget = $this->tmpDir . '/bar/bob.txt_moved';

        $this->assertTrue(is_array($this->sftp->stat($path)));
        $this->assertFalse($this->sftp->stat($pathTarget));

        $this->assertTrue($this->sftp->rename($path, $pathTarget));

        $this->assertTrue(is_array($this->sftp->stat($pathTarget)));
        $this->assertFalse($this->sftp->stat($path));
    }

    public function testListDirectory()
    {
        $path = $this->tmpDir . '/bar';

        $list = $this->sftp->listDirectory($path);

        $this->assertEquals(
          array(
            'files' => array($path . '/bob.txt'),
            'directories' => array($path . '/alice'),
            ),
            $list
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testListNotExistingDirectory()
    {
        $path = $this->tmpDir . '/does_not_exist';

        $this->sftp->listDirectory($path);
    }

    public function testCreateStatAndRemoveDir()
    {
        $path = $this->tmpDir . '/a_dir';

        // Check
        $this->assertFalse($this->sftp->stat($path));

        // Create
        $this->assertTrue($this->sftp->mkdir($path));

        //var_dump($this->sftp->stat($path)); die();

        $this->assertEquals(
          array(
            7,
            'size',
            4,
            'uid',
            5,
            'gid',
            2,
            'mode',
            8,
            'atime',
            9,
            'mtime'
          ),
          array_keys($this->sftp->stat($path))
        );


        // Remove
        $this->assertTrue($this->sftp->rmdir($path));
        $this->assertFalse($this->sftp->stat($path));
    }

    public function testTestNotExistingFile()
    {
        $path = $this->tmpDir . '/foo.bar';

        $this->assertFalse($this->sftp->exists($path));

        $list = $this->sftp->listDirectory($this->tmpDir);
        $this->assertNotContains($path, $list['files']);
    }

    public function testTestExistingFile()
    {
        $path = $this->tmpDir . '/foo.txt';

        $this->assertTrue($this->sftp->exists($path));

        $list = $this->sftp->listDirectory($this->tmpDir);
        $this->assertContains($path, $list['files']);
    }
}
 