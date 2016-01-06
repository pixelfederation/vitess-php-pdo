<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdoTest\PDO;

use VitessPdo\PDO\Vitess;
use VitessPdo\PDO\PDOStatement;
use VTCursor;
use Exception;

class PDOStatementTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testConstructor()
    {
        try {
            new PDOStatement("SELECT * FROM user", $this->getVitessStub());
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     *
     */
    public function testExecute()
    {
        $stmt = new PDOStatement("SELECT * FROM user", $this->getVitessStub(false));
        $result = $stmt->execute();

        $this->assertTrue($result);

        $users = $stmt->fetchAll();
        $this->assertInternalType('array', $users);
        $this->assertNotEmpty($users);
        $this->assertCount(2, $users);
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
            $stub->expects($this->any())->method('executeRead')
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

        $stub->expects($this->exactly(3))->method('next')
            ->will($this->onConsecutiveCalls(
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
