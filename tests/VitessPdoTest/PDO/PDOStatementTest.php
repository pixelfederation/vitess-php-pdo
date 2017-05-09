<?php
/**
 * Copyright 2017 PIXELFEDERATION s.r.o.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace VitessPdoTest\PDO;

use VitessPdo\PDO\Attributes;
use VitessPdo\PDO\Fetcher\Factory as FetcherFactory;
use VitessPdo\PDO\ParamProcessor;
use VitessPdo\PDO\QueryAnalyzer\Analyzer;
use VitessPdo\PDO\QueryExecutor\ResultInterface;
use VitessPdo\PDO\Vitess\Result;
use VitessPdo\PDO\Vitess\Cursor;
use VitessPdo\PDO\Vitess\Vitess;
use VitessPdo\PDO\PDOStatement;
use VitessPdo\PDO\Exception as VitessPDOException;
use Exception;
use PDO as CorePDO;

/**
 * Class PDOStatementTest
 *
 * @package VitessPdoTest\PDO
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
        $stmt = $this->getNewStatement(CorePDO::FETCH_BOTH, 0);
        $result = $stmt->execute();

        self::assertTrue($result);
        $this->expectException(VitessPDOException::class);
        $this->expectExceptionMessageRegExp('/^Fetch style not supported/');
        $stmt->fetchAll(CorePDO::FETCH_CLASS);
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

    public function testSetFetchMode()
    {
        $stmt = $this->getNewStatement(CorePDO::FETCH_BOTH);
        $result = $stmt->execute();
        $stmt->setFetchConfig(CorePDO::FETCH_ASSOC);

        self::assertTrue($result);
        $users = $stmt->fetchAll();
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);
        self::assertCount(2, $users);

        foreach ($users as $user) {
            self::assertArrayNotHasKey(0, $user);
            self::assertArrayHasKey('user_id', $user);
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

    public function testColumnCountAfterFetchAll()
    {
        $stmt = $this->getNewStatement(CorePDO::FETCH_BOTH);
        $stmt->execute();
        $stmt->fetchAll();
        $columnCount = $stmt->columnCount();

        self::assertEquals(2, $columnCount);
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

        $result = $this->getResultStub();
        $result->expects(self::any())->method('getCursor')
            ->willReturn($cursor);

        $vitess->expects(self::exactly(3))->method('executeWrite')
            ->willReturn($result);

        $stmt = new PDOStatement(
            "INSERT INTO user (`name`) VALUES (:name)",
            $vitess,
            new Attributes(),
            new ParamProcessor(),
            new Analyzer(),
            new FetcherFactory(),
            function (ResultInterface $result) {
                $result;
            }
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
     * @param int $nextCalls
     *
     * @return PDOStatement
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function getNewStatement($fetchMode = null, $nextCalls = 3)
    {
        return new PDOStatement(
            "SELECT user_id, name FROM user",
            $this->getVitessStub($fetchMode, $nextCalls),
            new Attributes(),
            new ParamProcessor(),
            new Analyzer(),
            new FetcherFactory(),
            function (ResultInterface $result) {
                $result;
            }
        );
    }

    /**
     * @param int $fetchMode
     * @param int $nextCalls
     *
     * @return Vitess
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function getVitessStub($fetchMode = null, $nextCalls = 3)
    {
        $stub = $this->getMockBuilder(Vitess::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($fetchMode) {
            $stub->expects(self::any())->method('executeRead')
                ->willReturn($this->getResultStubFetchBoth($nextCalls));

            $writeCursor = $this->getMockBuilder(Cursor::class)
                ->disableOriginalConstructor()
                ->getMock();

            $result = $this->getResultStub();
            $result->expects(self::any())->method('getCursor')
                ->willReturn($writeCursor);

            $stub->expects(self::any())->method('executeWrite')
                ->willReturn($result);
        }

        return $stub;
    }

    /**
     * @param int $nextCalls
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getResultStubFetchBoth($nextCalls = 3)
    {
        $stubCursor = $this->getMockBuilder(Cursor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stubCursor->expects(self::exactly($nextCalls))->method('next')
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

        $stubCursor->expects(self::any())->method('getFields')
            ->willReturn(['user_id', 'name']);

        $stub = $this->getResultStub();
        $stub->expects(self::any())->method('getCursor')
            ->willReturn($stubCursor);

        return $stub;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getResultStub()
    {
        $stub = $this->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects(self::any())->method('isSuccess')
            ->willReturn(true);

        return $stub;
    }
}
