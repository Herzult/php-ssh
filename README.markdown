PHP SSH
=======

[![Build Status](https://travis-ci.org/Herzult/php-ssh.png?branch=master)](https://travis-ci.org/Herzult/php-ssh) (master)
[![Build Status](https://travis-ci.org/Herzult/php-ssh.png?branch=development)](https://travis-ci.org/Herzult/php-ssh) (development)

Provides an object-oriented wrapper for the php ssh2 extension.

Requirements
------------

You need PHP version 5.3+ with the [SSH2 extension](http://www.php.net/manual/en/book.ssh2.php).

Installation
------------

Download the php-ssh library files in your project. 
If your project is versioned using git, the best is to add it as a submodule:

    $ git submodule add https://github.com/Herzult/php-ssh.git vendor/php-ssh

Then, you must add it to your autoloader. 
If you have no autoloader, you can use the one provided with the library.
Here is an exemple of how to use it:

    <?php

    require_once __DIR__.'/vendor/php-ssh/src/ClassLoader/UniversalClassLoader.php';

    use ClassLoader\UniversalClassLoader;

    $loader = new UniversalClassLoader();
    $loader->registerNamespaces(array(
        'Ssh' => __DIR__.'/vendor/php-ssh/src'
    ));

    $loader->register();

Installation (via composer)
------------

Download [composer](http://getcomposer.org/download/).
Install php-ssh using composer:

    $ php composer.phar require herzult/php-ssh:~1.0

Usage
-----

### Configuration of the connection

To etablish an SSH connection, you must firt define its configuration.
For that, create a Configuration instance with all the needed parameters.

    <?php

    // simple configuration to connect "my-host"
    $configuration = new Ssh\Configuration('my-host');

The availble configration classes are:

- `Configuration`
- `SshConfigFileConfiguration`

Both connection configuration and public/private key authetication can be obtained from a ssh config file such as `~/.ssh/config`

    <?php

    // simple configuration to connect "my-host"
    $configuration = new Ssh\SshConfigFileConfiguration('/Users/username/.ssh/config', 'my-host');
    $authentication = $configuration->getAuthentication('optional_passphrase', 'optional_username');

### Create a session

The session is the central access point to the SSH functionality provided by the library.

    <?php

    // ... the configuration creation

    $session = new Ssh\Session($configuration);

### Authentication

The authentication classes allow you to authenticate over a SSH session.
When you define an authentication for a session, it will authenticate on connection.

    <?php

    $configuration = new Ssh\Configuration('myhost');
    $authentication = new Ssh\Authentication\Password('John', 's3cr3t');

    $session = new Session($configuration, $authentication);

The available authentication are:

 - `None` for username based authentication
 - `Password` for password authentication
 - `PublicKeyFile` to authenticate using a public key
 - `HostBasedFile` to authenticate using a public hostkey
 - `Agent` to authenticate using an ssh-agent

### Authentication from SshConfigFileConfiguration

If you use an ssh config file you can load your authentication and configuration from it as follows:

    <?php

    $configuration = new Ssh\SshConfigFileConfiguration('~/.ssh/config', 'my-host');

    $session = new Session($configuration, $configuration->getAuthentication());

This will pick up your public and private keys from your config file Host and Identity declarations.

### Subsystems

Once you are authenticated over a SSH session, you can use the subsystems.

#### Sftp

You can easily access the sftp subsystem of a session using the `getSftp()` method:

    <?php

    // the session creation

    $sftp = $session->getSftp();

See the `Ssh\Sftp` class for more details on the available methods.

#### Publickey

The session provides also the `getPublickey()` method to access the publickey subsystem:

    <?php

    // ... the session creation

    $publickey = $session->getPublickey();

See the `Ssh\Publickey` class for more details on the available methods.

#### Exec

The session provides the `getExec()` method to access the exec subsystem

    <?php

    // ... the session creation

    $exec = $session->getExec();

    echo $exec->run('ls -lah');

See the `Ssh\Exec` class for more details.

