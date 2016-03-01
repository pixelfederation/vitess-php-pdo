# Vitess PDO driver

This library provides a PDO driver for [Vitess](http://vitess.io/). It is not a driver for the PDO PHP extension,
it's just a pure PHP implementation of the PDO interface. It is interchangeable with the original PDO class,
if no instanceof \PDO checks are present in the application code.

## Current state

Currently the code is beta quality, not all PDO methods and classes are implemented, but it already has been tested
in our application and it works properly. Everybody is welcome to create pull requests to implement some of the missing
things.

Also, the unit tests are currently bound to our Vitess dev instance, there is a plan to change this.
There's also not much documentation yet, but people requested the driver, so we decided to go open source.

## Usage

The [Vitess PHP client](https://github.com/youtube/vitess/tree/master/php), which is used in the PDO implementation 
has a dependency on the [PHP GRPC extension](https://github.com/grpc/grpc/tree/master/src/php), which has to be 
installed together with [GRPC](http://www.grpc.io/) and [Protobuf](https://developers.google.com/protocol-buffers/?hl=en).

To install the composer package, run the following command:

```bash
composer require pixelfederation/vitess-php-pdo
```

After the installation of the dependencies, the PDO class should be instantiated this way:

```php
$pdo = new \VitessPdo\PDO("vitess:dbname={$keyspace};host={$host};port={$port}");
```

- $keyspace: vitess keyspace (alternative to database name)
- $host: IP or hostname of Vtgate
- $port: Vtgate port

## Contribution

If you'd like to contribtue, we strongly recommend to run

```bash
./bin/setup-dev
```

from the project directory. This script will set up a commit hook, which checks the PSR/2 coding standards
using [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer) and also runs PHP linter and 
PHP Mess Detector [PHPMD](http://phpmd.org/)

## Known issues

On OS X there is "sometimes" a problem with the current GRPC version (0.13) and PHP GRPC extension (0.8)
(both of the versions are mandatory for the PDO driver and Vitess), meaning
that the GRPC requests to Vitess are a little bit slower [for an unknown reason](https://github.com/grpc/grpc/issues/4806). 
Linux is unaffected.

Also, there is no PHP 7 GRPC extension yet.