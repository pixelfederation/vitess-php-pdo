<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdoTest\PDO\Dsn;

use VitessPdo\PDO\Dsn\Driver;
use VitessPdo\PDO\Exception as DriverException;
use Exception;

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
            $this->fail("Error while constructing the driver: " . $e->getMessage());
        }

        $this->assertNotNull($driver);
        $this->assertEquals("vitess", $driver->getProtocol());
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
