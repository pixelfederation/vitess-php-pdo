<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdoTest;

use VitessPdo\PDO;
use VitessPdo\PDO\PDOStatement;
use Exception;
use PDOException;
use PDO as CorePDO;

/**
 * Class PDOTest
 *
 * @package VitessPdoTest
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class PDOTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    private $dsn = "vitess:dbname=test_keyspace;host=localhost;port=15991";

    /**
     *
     */
    public function testCorrectConstruct()
    {
        try {
            $pdo = new PDO($this->dsn);
            self::assertInstanceOf(PDO::class, $pdo);
        } catch (Exception $e) {
            self::fail(sprintf("Failed creating the PDO instance with an exception: '%s'", $e->getMessage()));
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
        $pdo = new PDO($this->dsn);
        $rows = $pdo->exec("INSERT INTO user (name) VALUES ('test_user')");

        self::assertEquals(1, $rows);
    }

    public function testTransactions()
    {
        $pdo = new PDO($this->dsn);

        self::assertEquals(false, $pdo->inTransaction());

        $commitResult = $pdo->commit();
        self::assertFalse($commitResult);

        $pdo->beginTransaction();
        self::assertEquals(true, $pdo->inTransaction());
        $rows = $pdo->exec("INSERT INTO user (name) VALUES ('test_user')");
        self::assertEquals(1, $rows);
        self::assertEquals(true, $pdo->inTransaction());
        $commitResult = $pdo->commit();
        self::assertTrue($commitResult);
        self::assertEquals(false, $pdo->inTransaction());
    }

    public function testTransactionRollbackException()
    {
        $pdo = new PDO($this->dsn);

        $this->expectException(PDOException::class);
        $this->expectExceptionMessage("No transaction is active.");
        $rollbackResult = $pdo->rollback();
        self::assertFalse($rollbackResult);
    }

    public function testTransactionRollback()
    {
        $pdo = new PDO($this->dsn);

        $pdo->beginTransaction();
        $rows = $pdo->exec("INSERT INTO user (name) VALUES ('test_user')");
        self::assertEquals(1, $rows);
        $rollbackResult = $pdo->rollback();
        self::assertTrue($rollbackResult);
        self::assertEquals(false, $pdo->inTransaction());
        // @todo - check if data exists in db
    }

    public function testLastInsertId()
    {
        $pdo = new PDO($this->dsn);

        $pdo->beginTransaction();
        $rows = $pdo->exec("INSERT INTO user (name) VALUES ('test_user')");
        self::assertEquals(1, $rows);
        self::assertNotEquals('0', $pdo->lastInsertId());
        $pdo->commit();
        self::assertEquals('0', $pdo->lastInsertId());
    }

    public function testPrepare()
    {
        $pdo = new PDO($this->dsn);
        $stmt = $pdo->prepare("SELECT * FROM user");

        self::assertInstanceOf(PDOStatement::class, $stmt);

        $result = $stmt->execute();
        self::assertTrue($result);

        $users = $stmt->fetchAll();
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);
    }

    public function testPrepareWithUnnamedParams()
    {
        $pdo = new PDO($this->dsn);
        $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id IN (?, ?)");

        self::assertInstanceOf(PDOStatement::class, $stmt);

        $result = $stmt->execute([151, 152]);
        self::assertTrue($result);

        $users = $stmt->fetchAll();
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);
        // warning! this doesn't have to work on sharded tables, if the data is in multiple shards
        self::assertCount(2, $users);
    }

    public function testPrepareWithNamedParams()
    {
        $pdo = new PDO($this->dsn);
        $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id IN (:id1, :id2)");

        self::assertInstanceOf(PDOStatement::class, $stmt);

        $result = $stmt->execute(['id1' => 151, 'id2' => 152]);
        self::assertTrue($result);

        $users = $stmt->fetchAll();
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);
        // warning! this doesn't have to work on sharded tables, if the data is in multiple shards
        self::assertCount(2, $users);
    }

    public function testPrepareWithNamedParamsString()
    {
        $pdo = new PDO($this->dsn);
        $stmt = $pdo->prepare("SELECT * FROM user WHERE name = :name");

        self::assertInstanceOf(PDOStatement::class, $stmt);

        $result = $stmt->execute(['name' => 'test_user']);
        self::assertTrue($result);

        $users = $stmt->fetchAll();
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);
    }

    public function testPrepareWithMixedParams()
    {
        $pdo = new PDO($this->dsn);
        $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id IN (:id1, ?)");

        self::assertInstanceOf(PDOStatement::class, $stmt);


        $result = $stmt->execute(['id1' => 151, 152]);
        self::assertFalse($result);
    }

    public function testPrepareWithUnnamedParamsBoundExtra()
    {
        $pdo = new PDO($this->dsn);
        $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id IN (?, ?)");

        self::assertInstanceOf(PDOStatement::class, $stmt);
        $stmt->bindValue(1, 151, CorePDO::PARAM_INT);
        $stmt->bindValue(2, 152, CorePDO::PARAM_INT);

        $result = $stmt->execute();
        self::assertTrue($result);

        $users = $stmt->fetchAll();
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);
        // warning! this doesn't have to work on sharded tables, if the data is in multiple shards
        self::assertCount(2, $users);
    }

    public function testPrepareWithNamedParamsBoundExtra()
    {
        $pdo = new PDO($this->dsn);
        $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id IN (:id1, :id2)");

        self::assertInstanceOf(PDOStatement::class, $stmt);
        $stmt->bindValue('id1', 151, CorePDO::PARAM_INT);
        $stmt->bindValue('id2', 152, CorePDO::PARAM_INT);

        $result = $stmt->execute();
        self::assertTrue($result);

        $users = $stmt->fetchAll();
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);
        // warning! this doesn't have to work on sharded tables, if the data is in multiple shards
        self::assertCount(2, $users);
    }

    public function testPrepareWithUnnamedParams2BoundExtra()
    {
        $pdo = new PDO($this->dsn);
        $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id IN (?, ?)");

        self::assertInstanceOf(PDOStatement::class, $stmt);
        $id1 = 151;
        $id2 = 152;
        $stmt->bindParam(1, $id1, CorePDO::PARAM_INT);
        $stmt->bindParam(2, $id2, CorePDO::PARAM_INT);

        $result = $stmt->execute();
        self::assertTrue($result);

        $users = $stmt->fetchAll();
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);
        // warning! this doesn't have to work on sharded tables, if the data is in multiple shards
        self::assertCount(2, $users);
    }

    public function testPrepareWithNamedParams2BoundExtra()
    {
        $pdo = new PDO($this->dsn);
        $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id IN (:id1, :id2)");

        self::assertInstanceOf(PDOStatement::class, $stmt);
        $id1 = 151;
        $id2 = 152;
        $stmt->bindParam('id1', $id1, CorePDO::PARAM_INT);
        $stmt->bindParam('id2', $id2, CorePDO::PARAM_INT);

        $result = $stmt->execute();
        self::assertTrue($result);

        $users = $stmt->fetchAll();
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);
        // warning! this doesn't have to work on sharded tables, if the data is in multiple shards
        self::assertCount(2, $users);
    }
}
