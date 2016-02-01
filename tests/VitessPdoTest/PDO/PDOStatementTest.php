<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdoTest\PDO;

use VitessPdo\PDO\Attributes;
use VitessPdo\PDO\ParamProcessor;
use VitessPdo\PDO\QueryAnalyzer;
use VitessPdo\PDO\Vitess;
use VitessPdo\PDO\PDOStatement;
use VitessPdo\PDO\Exception as VitessPDOException;
use Vitess\Cursor;
use Exception;
use PDO as CorePDO;

/**
 * Class PDOStatementTest
 *
 * @package VitessPdoTest\PDO
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class PDOStatementTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testConstructor()
    {
        try {
            $this->getNewStatement();
        } catch (Exception $e) {
            self::fail($e->getMessage());
        }
    }

    /**
     *
     */
    public function testExecute()
    {
        $stmt = $this->getNewStatement(CorePDO::FETCH_BOTH);
        $result = $stmt->execute();

        self::assertTrue($result);

        $users = $stmt->fetchAll();
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);
        self::assertCount(2, $users);
    }

    /**
     *
     */
    public function testExecuteFetchAll()
    {
        $stmt = $this->getNewStatement(CorePDO::FETCH_BOTH);
        $result = $stmt->execute();

        self::assertTrue($result);

        $users = $stmt->fetchAll(CorePDO::FETCH_BOTH);
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);
        self::assertCount(2, $users);

        foreach ($users as $user) {
            self::assertArrayHasKey(0, $user);
            self::assertArrayHasKey('user_id', $user);
        }

        $stmt = $this->getNewStatement(CorePDO::FETCH_ASSOC);
        $stmt->execute();

        $users = $stmt->fetchAll(CorePDO::FETCH_ASSOC);
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);
        self::assertCount(2, $users);

        foreach ($users as $user) {
            self::assertArrayNotHasKey(0, $user);
            self::assertArrayHasKey('user_id', $user);
        }

        $stmt = $this->getNewStatement(CorePDO::FETCH_NUM);
        $stmt->execute();

        $users = $stmt->fetchAll(CorePDO::FETCH_NUM);
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);
        self::assertCount(2, $users);

        foreach ($users as $user) {
            self::assertArrayHasKey(0, $user);
            self::assertArrayNotHasKey('user_id', $user);
        }
    }

    /**
     *
     */
    public function testExecuteFetchAllFetchColumn()
    {
        $stmt = $this->getNewStatement(CorePDO::FETCH_BOTH);
        $result = $stmt->execute();

        self::assertTrue($result);

        $usersIds = $stmt->fetchAll(CorePDO::FETCH_COLUMN, 0);
        self::assertInternalType('array', $usersIds);
        self::assertNotEmpty($usersIds);
        self::assertCount(2, $usersIds);

        foreach ($usersIds as $userId) {
            self::assertInternalType('string', $userId);
        }
    }

    /**
     *
     */
    public function testExecuteFetchAllFetchKeyPairs()
    {
        $stmt = $this->getNewStatement(CorePDO::FETCH_BOTH);
        $result = $stmt->execute();

        self::assertTrue($result);

        $users = $stmt->fetchAll(CorePDO::FETCH_KEY_PAIR);
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);
        self::assertCount(2, $users);

        foreach ($users as $userId => $name) {
            self::assertTrue((int) $userId > 0);
            self::assertInternalType('string', $name);
        }
    }

    /**
     *
     */
    public function testFetch()
    {
        $stmt = $this->getNewStatement(CorePDO::FETCH_BOTH);
        $result = $stmt->execute();

        self::assertTrue($result);
        $count = 0;

        while (($row = $stmt->fetch()) !== false) {
            $count++;
            self::assertInternalType('array', $row);
            self::assertNotEmpty($row);
        }

        self::assertEquals(2, $count);
    }

    /**
     *
     */
    public function testFetchStyleNotImplementedException()
    {
        $stmt = $this->getNewStatement(CorePDO::FETCH_BOTH);
        $result = $stmt->execute();

        self::assertTrue($result);
        $this->expectException(VitessPDOException::class);
        $this->expectExceptionMessageRegExp('/^Fetch style not supported/');
        $stmt->fetchAll();
        $stmt->fetch(CorePDO::FETCH_CLASS);
    }

    /**
     *
     */
    public function testFetchColumn()
    {
        $stmt = $this->getNewStatement(CorePDO::FETCH_BOTH);
        $result = $stmt->execute();

        self::assertTrue($result);
        $count = 0;

        while (($userId = $stmt->fetchColumn()) !== false) {
            $count++;
            self::assertInternalType('string', $userId);
            self::assertGreaterThan(0, (int) $userId);
            self::assertEquals($count, $userId);
        }

        self::assertEquals(2, $count);

        $stmt = $this->getNewStatement(CorePDO::FETCH_BOTH);
        $result = $stmt->execute();

        self::assertTrue($result);
        $count = 0;

        while (($userId = $stmt->fetchColumn(1)) !== false) {
            $count++;
            self::assertInternalType('string', $userId);
            self::assertEquals("user_{$count}", $userId);
        }
    }

    /**
     *
     */
    public function testCloseCursor()
    {
        $stmt = $this->getNewStatement(CorePDO::FETCH_BOTH);
        $stmt->execute();
        $stmt->fetch();
        $stmt->fetch();
        $stmt->fetch();
        self::assertTrue($stmt->closeCursor());
    }

    /**
     *
     */
    public function testFetchColumnAfterFetchAll()
    {
        $stmt = $this->getNewStatement(CorePDO::FETCH_BOTH);
        $stmt->execute();
        $stmt->fetchAll();
        $userId = $stmt->fetchColumn(0);

        self::assertEquals('1', $userId);
    }

    public function testExecuteInsert()
    {
        $vitess = $this->getMockBuilder(Vitess::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cursor = $this->getMockBuilder(Cursor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cursor->expects(self::exactly(3))->method('getRowsAffected')
            ->willReturn(1);

        $vitess->expects(self::exactly(3))->method('executeWrite')
            ->willReturn($cursor);

        $stmt = new PDOStatement(
            "INSERT INTO user (`name`) VALUES (:name)",
            $vitess,
            new Attributes(),
            new ParamProcessor(),
            new QueryAnalyzer()
        );

        for ($i = 0; $i < 3; $i++) {
            $result = $stmt->execute([
                'name' => 'test___' . $i
            ]);

            self::assertTrue($result);
            self::assertEquals(1, $stmt->rowCount());
        }
    }

    /**
     * @param int $fetchMode
     *
     * @return PDOStatement
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function getNewStatement($fetchMode = null)
    {
        return new PDOStatement(
            "SELECT user_id, name FROM user",
            $this->getVitessStub($fetchMode),
            new Attributes(),
            new ParamProcessor(),
            new QueryAnalyzer()
        );
    }

    /**
     * @param int $fetchMode
     *
     * @return Vitess
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function getVitessStub($fetchMode = null)
    {
        $stub = $this->getMockBuilder(Vitess::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($fetchMode) {
            $stub->expects(self::any())->method('executeRead')
                ->willReturn($this->getVTCursorStubFetchBoth());

            $writeCursor = $this->getMockBuilder(Cursor::class)
                ->disableOriginalConstructor()
                ->getMock();

            $stub->expects(self::any())->method('executeWrite')
                ->willReturn($writeCursor);
        }

        return $stub;
    }

    /**
     * @return Cursor
     */
    private function getVTCursorStubFetchBoth()
    {
        $stub = $this->getMockBuilder(Cursor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects(self::exactly(3))->method('next')
            ->will(self::onConsecutiveCalls(
                [
                    0         => '1',
                    'user_id' => '1',
                    1         => 'user_1',
                    'name'    => 'user_1'
                ],
                [
                    0         => '2',
                    'user_id' => '2',
                    1         => 'user_2',
                    'name'    => 'user_2'
                ],
                false
            ));

        return $stub;
    }
}
