<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdoTest;

use VitessPdo\PDO;
use Exception;
use PDOException;

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

    public function testTransactions()
    {
        $dsn = "vitess:dbname=test_keyspace;host=localhost;port=15991";

        $pdo = new PDO($dsn);

        $this->assertEquals(false, $pdo->inTransaction());

        $commitResult = $pdo->commit();
        $this->assertFalse($commitResult);

        $pdo->beginTransaction();
        $this->assertEquals(true, $pdo->inTransaction());
        $rows = $pdo->exec("INSERT INTO user (name) VALUES ('test_user')");
        $this->assertEquals(1, $rows);
        $this->assertEquals(true, $pdo->inTransaction());
        $commitResult = $pdo->commit();
        $this->assertTrue($commitResult);
        $this->assertEquals(false, $pdo->inTransaction());
    }

    public function testTransactionRollbackException()
    {
        $dsn = "vitess:dbname=test_keyspace;host=localhost;port=15991";

        $pdo = new PDO($dsn);

        $this->expectException(PDOException::class);
        $this->expectExceptionMessage("No transaction is active.");
        $rollbackResult = $pdo->rollback();
        $this->assertFalse($rollbackResult);
    }

    public function testTransactionRollback()
    {
        $dsn = "vitess:dbname=test_keyspace;host=localhost;port=15991";

        $pdo = new PDO($dsn);

        $pdo->beginTransaction();
        $rows = $pdo->exec("INSERT INTO user (name) VALUES ('test_user')");
        $this->assertEquals(1, $rows);
        $rollbackResult = $pdo->rollback();
        $this->assertTrue($rollbackResult);
        $this->assertEquals(false, $pdo->inTransaction());
        // @todo - check if data exists in db
    }

    public function testLastInsertId()
    {
        $dsn = "vitess:dbname=test_keyspace;host=localhost;port=15991";

        $pdo = new PDO($dsn);

        $pdo->beginTransaction();
        $rows = $pdo->exec("INSERT INTO user (name) VALUES ('test_user')");
        $this->assertEquals(1, $rows);
        $this->assertNotEquals('0', $pdo->lastInsertId());
        $pdo->commit();
        $this->assertEquals('0', $pdo->lastInsertId());
    }
}
