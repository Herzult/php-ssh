# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.0.0 - TBD

### Added

- SftpDirectoryIterator to the sftp subsystem.
- Exception class for authentication failures

### Changed

- Directory listing and traversing to RecursiveIterator pattern
- Moved open ssh config related code to `Ssh\OpenSSH` namespace
- Authenticators will now only accept instances of `Ssh\Session`
- Changed `Ssh\Configuration` to be an interface. The detail implementation is now called `Ssh\HostConfiguration`
- Renamed `AbstractResourceHolder` to `AbstractResourceProvider` 
- `Sftp::scanDirectory` now returns a flat array containing all entries. Directories can be identified by a trailing 
  slash

### Deprecated

- Nothing.

### Removed

- Support for PHP < 7.1
- Recursive option from `Sftp::scanDirectory`

### Fixed

- Nothing.
