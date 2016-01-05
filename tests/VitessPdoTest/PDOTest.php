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
        $dsn = "vitess:dbname=test_keyspace;host=localhost;port=15991";

        try {
            $pdo = new PDO($dsn);
            $this->assertInstanceOf(PDO::class, $pdo);
        } catch (Exception $e) {
            $this->fail(sprintf("Failed creating the PDO instance with an exception: '%s'", $e->getMessage()));
        }
    }

//    /**
//     * Vitess doesn't support SET NAMES queries
//     */
//    public function testCorrectConstructInitQuery()
//    {
//        $dsn = "vitess:dbname=test_keyspace;host=localhost;port=15991";
//        $options = [
//            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8 COLLATE 'utf8_bin', time_zone='+0:00';",
//        ];
//
//        try {
//            $pdo = new PDO($dsn, null, null, $options);
//            $this->assertInstanceOf(PDO::class, $pdo);
//        } catch (Exception $e) {
//            print_r($e->getPrevious());
//            $this->fail(sprintf("Failed creating the PDO instance with an exception: '%s'", $e->getMessage()));
//        }
//    }

    public function testExecFunctionInsert()
    {
        $dsn = "vitess:dbname=test_keyspace;host=localhost;port=15991";

        $pdo = new PDO($dsn);
        $rows = $pdo->exec("INSERT INTO user (name) VALUES ('test_user')");

        $this->assertEquals(1, $rows);
    }
}
