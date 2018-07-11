PHP SSH
=======

[![Build Status](https://travis-ci.org/lukanetconsult/php-ssh.png?branch=develop)](https://travis-ci.org/lukanetconsult/php-ssh) (develop)

Provides an object-oriented wrapper for the php ssh2 extension. This is based on the work of
[Antoine Hérault](https://github.com/Herzult/php-ssh).

Requirements
------------

You need PHP version 7.1+ with the [SSH2 extension](http://www.php.net/manual/en/book.ssh2.php).

Installation
------------

The best way to add the library to your project is using [composer](http://getcomposer.org).

    $ composer require luka/php-ssh:^2.0

Usage
-----

### Configuration of the connection

To establish an SSH connection, you must first define its configuration.
For that, create a Configuration instance with all the needed parameters.

```php
<?php

// simple configuration to connect "my-host"
$configuration = new Ssh\HostConfiguration('my-host');
```

The available configuration classes are:

- `Ssh\HostConfiguration`
- `Ssh\OpenSSH\ConfigFile`

Both connection configuration and public/private key authentication can be obtained from a ssh config file such as `~/.ssh/config`

```php
<?php

// simple configuration to connect "my-host"
$configuration = Ssh\OpenSSH\ConfigFile::fromHostname('my-host', '~/.ssh/config');
$authentication = $configuration->createAuthenticationMethod('optional_passphrase', 'optional_username');
```

### Create a session

The session is the central access point to the SSH functionality provided by the library.

```php
<?php
// ... the configuration creation
$session = new Ssh\Session($configuration);
```

### Authentication

The authentication classes allow you to authenticate over a SSH session.
When you define an authentication for a session, it will authenticate on connection.

```php
<?php

$configuration = new Ssh\HostConfiguration('myhost');
$authentication = new Ssh\Authentication\Password('John', 's3cr3t');

$session = new Session($configuration, $authentication);
```

The available authentication are:

 - `None` for username based authentication
 - `Password` for password authentication
 - `PublicKeyFile` to authenticate using a public key
 - `HostBasedFile` to authenticate using a public hostkey
 - `Agent` to authenticate using an ssh-agent

### Authentication from `Ssh\OpenSSH\ConfigFile`

If you use an ssh config file you can load your authentication and configuration from it as follows:

```php
<?php

$configuration = Ssh\OpenSSH\ConfigFile::fromHostname('my-host');
$session = new Ssh\Session($configuration, $configuration->createAuthenticationMethod());
```

This will pick up the username, and your public and private keys from your config file Host and 
Identity declarations.

This simple snippet only works if the `User` declaration is also present, and the private key does
not require a pass phrase. If any of this is not the case you have to pass the missing values to
the `createAuthentication()` method.

### Subsystems

Once you are authenticated over a SSH session, you can use the subsystems.

#### Sftp

You can easily access the sftp subsystem of a session using the `getSftp()` method:

```php
<?php

// the session creation
$sftp = $session->getSftp();
```

See the `Ssh\Sftp` class for more details on the available methods.

#### Publickey

The session also provides the `getPublickey()` method to access the publickey subsystem:

```php
<?php

// ... the session creation
$publickey = $session->getPublickey();
```

The Public-Key subsystem allows you to provide multiple public keys to use for authentication.
See the `Ssh\Publickey` class for more details on the available methods.

#### Exec

The session provides the `getExec()` method to access the exec subsystem

```php
<?php

// ... the session creation

/** @var Ssh\Session $session */
$exec = $session->getExec();
echo $exec->run('ls -lah')->getExitCode(), PHP_EOL;
```

See the `Ssh\Exec` class for more details.
