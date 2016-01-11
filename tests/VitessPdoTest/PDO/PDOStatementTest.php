<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdoTest\PDO;

use VitessPdo\PDO\Attributes;
use VitessPdo\PDO\ParamProcessor;
use VitessPdo\PDO\Vitess;
use VitessPdo\PDO\PDOStatement;
use VTCursor;
use Exception;

/**
 * Class PDOStatementTest
 *
 * @package VitessPdoTest\PDO
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
        $stmt = $this->getNewStatement(false);
        $result = $stmt->execute();

        self::assertTrue($result);

        $users = $stmt->fetchAll();
        self::assertInternalType('array', $users);
        self::assertNotEmpty($users);
        self::assertCount(2, $users);
    }

    /**
     * @param boolean $empty
     *
     * @return PDOStatement
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function getNewStatement($empty = true)
    {
        return new PDOStatement(
            "SELECT * FROM user",
            $this->getVitessStub($empty),
            new Attributes(),
            new ParamProcessor()
        );
    }

    /**
     * @param boolean $empty
     * @return Vitess
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function getVitessStub($empty = true)
    {
        $stub = $this->getMockBuilder(Vitess::class)
                    ->disableOriginalConstructor()
                    ->getMock();

        if (!$empty) {
            $stub->expects(self::any())->method('executeRead')
                ->willReturn($this->getVTCursorStub());
        }

        return $stub;
    }

    /**
     * @return VTCursor
     */
    private function getVTCursorStub()
    {
        $stub = $this->getMockBuilder(VTCursor::class)
                     ->disableOriginalConstructor()
                     ->getMock();

        $stub->expects(self::exactly(3))->method('next')
            ->will(self::onConsecutiveCalls(
                [
                    'user_id' => 1,
                    'name' => 'user_1'
                ],
                [
                    'user_id' => 2,
                    'name' => 'user_2'
                ],
                false
            ));

        return $stub;
    }
}
