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
        $configString = "dbname=testdb;host=localhost;port=15991";
        $config = null;

        try {
            $config = new Config($configString);
        } catch (Exception $e) {
            self::fail("Error while constructing the driver: " . $e->getMessage());
        }

        self::assertNotNull($config);
        self::assertEquals("localhost", $config->getHost());
        self::assertEquals("testdb", $config->getDbName());
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
        $configString = "dbname=testdb";

        $this->expectException(DriverException::class);
        $this->expectExceptionMessage("Invalid config - host missing.");
        new Config($configString);
    }

    public function testDbNameMissing()
    {
        $configString = "host=localhost";

        $this->expectException(DriverException::class);
        $this->expectExceptionMessage("Invalid config - db name missing.");
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
