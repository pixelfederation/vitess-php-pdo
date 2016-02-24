<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdoTest;

use VitessPdo\PDO;
use VitessPdo\PDO\PDOStatement;
use VitessPdo\PDO\Exception as VitessPDOException;
use Exception;
use PDOException;
use PDO as CorePDO;

/**
 * Class PDOTest
 *
 * @package VitessPdoTest
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class PDOTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    private $dsn = "vitess:dbname=test_keyspace;host=localhost;port=15991";

    /**
     * @var array
     */
    private $errors;

    /**
     *
     */
    protected function setUp()
    {
        $this->errors = [];
        set_error_handler([$this, "errorHandler"]);
    }

    /**
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $this->errors[] = compact("errno", "errstr", "errfile", "errline", "errcontext");
    }

    /**
     * @param int $errno
     */
    public function assertError($errno)
    {
        foreach ($this->errors as $error) {
            if ($error["errno"] === $errno) {
                return;
            }
        }
        self::fail(
            "Error with level " . $errno . " not found in " .
            var_export($this->errors, true)
        );
    }

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

    public function testExecFunctionInsertAndStmtDeleteReused()
    {
        $pdo = new PDO($this->dsn);
        $insertIds = [];

        for ($i = 0; $i < 3; $i++) {
            $rows = $pdo->exec("INSERT INTO user (name) VALUES ('test_user')");
            $insertIds[] = $pdo->lastInsertId();

            self::assertEquals(1, $rows);
        }

        $stmt = $pdo->prepare("DELETE FROM user WHERE user_id = :id");

        foreach ($insertIds as $id) {
            $result = $stmt->execute(['id' => $id]);
            self::assertTrue($result);
            self::assertEquals(1, $stmt->rowCount());
        }
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

    public function testReadWhileInTransactions()
    {
        $pdo = new PDO($this->dsn);

        self::assertEquals(false, $pdo->inTransaction());

        $commitResult = $pdo->commit();
        self::assertFalse($commitResult);

        $pdo->beginTransaction();
        self::assertEquals(true, $pdo->inTransaction());
        $rows = $pdo->exec("INSERT INTO user (name) VALUES ('test_user')");
        $lastId = $pdo->lastInsertId();
        self::assertEquals(1, $rows);
        self::assertEquals(true, $pdo->inTransaction());
        $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = {$lastId}");
        $stmt->execute();
        $user = $stmt->fetch();
        self::assertEquals((string) $lastId, $user['user_id']);
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
        $name = 'test_user_rollback';

        $pdo->beginTransaction();
        $rows = $pdo->exec("INSERT INTO user (name) VALUES ('{$name}')");
        self::assertEquals(1, $rows);
        $rollbackResult = $pdo->rollback();
        self::assertTrue($rollbackResult);
        self::assertEquals(false, $pdo->inTransaction());

        $stmt = $pdo->prepare("SELECT * FROM user WHERE name = :name");
        $result = $stmt->execute(['name' => $name]);
        self::assertTrue($result);

        $users = $stmt->fetchAll();
        self::assertEmpty($users);
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

        $this->expectException(PDOException::class);
        $stmt->execute(['id1' => 151, 152]);
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
        $count = 0;

        self::assertInternalType('array', $users);
        foreach ($users as $user) {
            $count++;
            self::assertInternalType('array', $user);
            self::assertNotEmpty($user);
            self::assertArrayHasKey('user_id', $user);
            self::assertArrayHasKey(0, $user);
        }

        // warning! this doesn't have to work on sharded tables, if the data is in multiple shards
        self::assertEquals(2, $count);
    }

    public function testPrepareWithNamedParams2BoundExtraFetchAllAssoc()
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

        $users = $stmt->fetchAll(CorePDO::FETCH_ASSOC);
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);

        $count = 0;

        foreach ($users as $user) {
            $count++;
            self::assertInternalType('array', $user);
            self::assertNotEmpty($user);
            self::assertArrayHasKey('user_id', $user);
            self::assertArrayNotHasKey(0, $user);
        }

        // warning! this doesn't have to work on sharded tables, if the data is in multiple shards
        self::assertEquals(2, $count);
    }

    public function testPrepareWithNamedParams2BoundExtraFetchAllNum()
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

        $users = $stmt->fetchAll(CorePDO::FETCH_NUM);
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);

        $count = 0;

        foreach ($users as $user) {
            $count++;
            self::assertInternalType('array', $user);
            self::assertNotEmpty($user);
            self::assertArrayNotHasKey('user_id', $user);
            self::assertArrayHasKey(0, $user);
        }

        // warning! this doesn't have to work on sharded tables, if the data is in multiple shards
        self::assertEquals(2, $count);
    }

    public function testPrepareWithNamedParams2BoundExtraFetch()
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

        $count = 0;

        while (($user = $stmt->fetch()) !== false) {
            $count++;
            self::assertInternalType('array', $user);
            self::assertNotEmpty($user);
        }

        // warning! this doesn't have to work on sharded tables, if the data is in multiple shards
        self::assertEquals(2, $count);
    }

    public function testPrepareWithNamedParams2BoundExtraFetchColumn()
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

        $count = 0;

        while (($userId = $stmt->fetchColumn()) !== false) {
            self::assertInternalType('string', $userId);
            // order is not ensured and ORDER BY cannot be used because of the multi shard query
            self::assertTrue(in_array($userId, [$id1, $id2]));
            $count++;
        }

        // warning! this doesn't have to work on sharded tables, if the data is in multiple shards
        self::assertEquals(2, $count);
    }

    public function testPrepareWithEmptyResult()
    {
        $pdo = new PDO($this->dsn);
        $stmt = $pdo->prepare("SELECT * FROM user WHERE name = :name");

        self::assertInstanceOf(PDOStatement::class, $stmt);

        $result = $stmt->execute(['name' => 'non_existent_user']);
        self::assertTrue($result);

        $users = $stmt->fetchAll();
        self::assertInternalType('array', $users);
        self::assertEmpty($users);

        $users = $stmt->fetchAll(CorePDO::FETCH_ASSOC);
        self::assertInternalType('array', $users);
        self::assertEmpty($users);
    }

    public function testSetAttributeNotImplemented()
    {
        $this->expectException(VitessPDOException::class);
        $this->expectExceptionMessageRegExp('/^PDO parameter not implemented/');
        $pdo = new PDO($this->dsn);
        $pdo->setAttribute(CorePDO::ATTR_CASE, CorePDO::CASE_LOWER);
    }

    public function testSetAttribute()
    {
        $pdo = new PDO($this->dsn);
        $result = $pdo->setAttribute(CorePDO::ATTR_ERRMODE, CorePDO::ERRMODE_SILENT);
        self::assertTrue($result);
    }

    public function testSetAttributeErrModeSilent()
    {
        $pdo = new PDO($this->dsn);

        $pdo->setAttribute(CorePDO::ATTR_ERRMODE, CorePDO::ERRMODE_SILENT);
        $stmt = $pdo->prepare("SELECT * FROM non_existent_table");
        $result = $stmt->execute();
        self::assertFalse($result);
    }

    /**
     *
     */
    public function testSetAttributeErrModeWarning()
    {
        $pdo = new PDO($this->dsn);

        $pdo->setAttribute(CorePDO::ATTR_ERRMODE, CorePDO::ERRMODE_WARNING);
        $stmt = $pdo->prepare("SELECT * FROM non_existent_table");
        $result = $stmt->execute();
        $this->assertError(E_WARNING);
        self::assertFalse($result);
    }

    public function testSetAttributeErrModeException()
    {
        $pdo = new PDO($this->dsn);

        $this->expectException(PDOException::class);

        $pdo->setAttribute(CorePDO::ATTR_ERRMODE, CorePDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("SELECT * FROM non_existent_table");
        $result = $stmt->execute();
        self::assertFalse($result);
    }

    public function testGetAttribute()
    {
        $pdo = new PDO($this->dsn);

        $result = $pdo->getAttribute(CorePDO::ATTR_ERRMODE);
        self::assertEquals(CorePDO::ERRMODE_EXCEPTION, $result);

        $result = $pdo->getAttribute(CorePDO::ATTR_ORACLE_NULLS);
        self::assertNull($result);
    }

    public function testGetAttributeDriverName()
    {
        $pdo = new PDO($this->dsn);

        $driverName = $pdo->getAttribute(CorePDO::ATTR_DRIVER_NAME);
        self::assertEquals(PDO\Attributes::DRIVER_NAME, $driverName);
    }

    public function testQuote()
    {
        $pdo = new PDO($this->dsn);

        $str1 = $pdo->quote('Nice');
        self::assertEquals("'Nice'", $str1);

        $str2 = $pdo->quote('Naughty \' string');
        self::assertEquals("'Naughty '' string'", $str2);

        $str3 = $pdo->quote("Co'mpl''ex \"st'\"ring");
        self::assertEquals("'Co''mpl''''ex \"st''\"ring'", $str3);
    }

    public function testQuery()
    {
        $pdo = new PDO($this->dsn);
        $stmt = $pdo->query("SELECT * FROM user");

        self::assertInstanceOf(PDOStatement::class, $stmt);

        $users = $stmt->fetchAll();
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);

        $stmt = $pdo->query("SELECT * FROM non_existent_table");

        self::assertFalse($stmt);
    }
}
