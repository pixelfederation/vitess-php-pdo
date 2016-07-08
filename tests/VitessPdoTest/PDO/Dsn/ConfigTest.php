<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdoTest\PDO\Dsn;

use VitessPdo\PDO\Dsn\Config;
use VitessPdo\PDO\Exception as DriverException;
use Exception;

/**
 * Class ConfigTest
 *
 * @package VitessPdoTest\PDO\Dsn
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testConstruct()
    {
        $configString = "keyspace=testdb;host=localhost;port=15991;cell=testcell";
        $config = null;

        try {
            $config = new Config($configString);
        } catch (Exception $e) {
            self::fail("Error while constructing the driver: " . $e->getMessage());
        }

        self::assertNotNull($config);
        self::assertEquals("localhost", $config->getHost());
        self::assertEquals("testdb", $config->getKeyspace());
        self::assertEquals(15991, $config->getPort());
        self::assertEquals('testcell', $config->getCell());
    }

    public function testBadConstruct()
    {
        $configString = "port=15991";
        $this->expectException(DriverException::class);
        $this->expectExceptionMessage('Invalid config - host missing.');

        new Config($configString);
    }

    public function testConstructWithoutKeyspace()
    {
        $configString = "host=localhost;port=15991";
        $config = null;

        try {
            $config = new Config($configString);
        } catch (Exception $e) {
            self::fail("Error while constructing the driver: " . $e->getMessage());
        }

        self::assertNotNull($config);
        self::assertEquals("localhost", $config->getHost());
        self::assertEquals("", $config->getKeyspace());
        self::assertEquals(15991, $config->getPort());
    }

    public function testParamMissing()
    {
        $configString = "host=localhost;param1";

        $this->expectException(DriverException::class);
        $this->expectExceptionMessageRegExp("/^Invalid parameter -.*/");
        new Config($configString);
    }

    public function testHostMissing()
    {
        $configString = "keyspace=testdb";

        $this->expectException(DriverException::class);
        $this->expectExceptionMessage("Invalid config - host missing.");
        new Config($configString);
    }

    public function testPortIsNotInteger()
    {
        $configString = "host=localhost;dbname=testdb;port=asd";

        $this->expectException(DriverException::class);
        $this->expectExceptionMessage("Invalid config - port has to be a positive integer.");
        new Config($configString);
    }
}
