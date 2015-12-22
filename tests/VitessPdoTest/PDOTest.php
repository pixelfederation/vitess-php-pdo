<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdoTest;

use VitessPdo\PDO;
use Exception;

class PDOTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testCorrectConstruct()
    {
        $dsn = "vitess:dbname=testdb;host=localhost;port=15991";

        try {
            $pdo = new PDO($dsn);
            $this->assertInstanceOf(PDO::class, $pdo);
        } catch (Exception $e) {
            $this->fail(sprintf("Failed creating the PDO instance with an exception: '%s'", $e->getMessage()));
        }
    }
}
