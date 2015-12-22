<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdoTest\PDO;

use VitessPdo\PDO\Dsn;
use VitessPdo\PDO\Exception as DriverException;
use Exception;

class DsnTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testConstruct()
    {
        $dsnString = "vitess:dbname=testdb;host=localhost;port=15991";
        $dsn = null;

        try {
            $dsn = new Dsn($dsnString);
        } catch (Exception $e) {
            $this->fail("Error while constructing Dsn object: " . $e->getMessage());
        }

        $this->assertNotNull($dsn);
        $this->assertNotNull($dsn->getDriver());
        $this->assertNotNull($dsn->getConfig());
    }

    /**
     *
     */
    public function testWrongDsn()
    {
        $dsnString = "vitess-dbname=testdb;host=localhost;port=15991";

        $this->expectException(DriverException::class);
        $this->expectExceptionMessage("Invalid dsn string - exactly one colon has to be present.");
        new Dsn($dsnString);
    }
}
