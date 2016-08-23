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

This driver isn't compatible with any official MySql/MariaDB management tools, but 
we created an [Adminer fork](https://github.com/pixelfederation/adminer), which you can use to have some insight
about the data in your Vitess cluster.

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
$pdo = new \VitessPdo\PDO("keyspace={$keyspace};host={$host};port={$port}");
```

- $keyspace: vitess keyspace (alternative to database name)
- $host: IP or hostname of Vtgate
- $port: Vtgate port

If you want to use some limited DDL support, you have to instantiate the PDO instance this way:

```php
$pdo = new \VitessPdo\PDO("vitess:cell={$cell};keyspace={$keyspace};host={$host};port={$port};vtctld_host={vtcrld_host};vtctld_port={vtctld_port}");
```

- $cell: name of the Vitess cell
- $vtctld_host: IP or hostname of Vtctld
- $vtctld_port: Vtctld port

Only a limited subset of DDL queries is supported (the goal of the implementation was to get the PDO driver
work with [Adminer](https://github.com/pixelfederation/adminer).

## Contribution

If you'd like to contribtue, we strongly recommend to run

```bash
./bin/setup-dev
```

from the project directory. This script will set up a commit hook, which checks the PSR/2 coding standards
using [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer) and also runs PHP linter and 
PHP Mess Detector [PHPMD](http://phpmd.org/)

To be able to run the unit tests, you need to have installed Vitess locally for now (there is a guide 
for [Ubuntu and OS X](http://vitess.io/getting-started/local-instance.html)). 

The tests need to be run from a shell which has set all the environment variables from the guide,
to be ready to run vitess instances. After setting the enviroment variables, 
just got to the library root folder and run the following command:

```bash
./bin/phpunit
```

If there is a need to perform any sql queries in the newly written unit tests,
there is a database schema provided in the **tests/vitess/schema** folder.
The schema contains two shards - **lookup** and **user**.

## Known issues

On OS X there ~~is~~ was "sometimes" a problem with the current GRPC version (0.13) and PHP GRPC extension (0.8)
(both of the versions are mandatory for the PDO driver and Vitess), meaning
that the GRPC requests to Vitess are a little bit slower [for an unknown reason](https://github.com/grpc/grpc/issues/4806). 
Linux is unaffected.

~~Also, there is no PHP 7 GRPC extension yet.~~

The first issue was resolved in grpc [1.0.0RC1 and 1.0.0RC2](https://pecl.php.net/package/gRPC), 
the extension should be stable now.