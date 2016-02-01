<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdoTest\PDO\Dsn;

use VitessPdo\PDO\Dsn\Driver;
use VitessPdo\PDO\Exception as DriverException;
use Exception;

/**
 * Class DriverTest
 *
 * @package VitessPdoTest\PDO\Dsn
 */
class DriverTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testConstruct()
    {
        $protocol = "vitess";
        $driver = null;

        try {
            $driver = new Driver($protocol);
        } catch (Exception $e) {
            self::fail("Error while constructing the driver: " . $e->getMessage());
        }

        self::assertNotNull($driver);
        self::assertEquals("vitess", $driver->getProtocol());
    }

    /**
     *
     */
    public function testIncorrectConstruct()
    {
        $protocol = "mysql";

        $this->expectException(DriverException::class);
        new Driver($protocol);
    }
}
