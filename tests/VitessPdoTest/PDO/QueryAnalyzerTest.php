<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 */

namespace VitessPdoTest\PDO;

use VitessPdo\PDO\QueryAnalyzer\Analyzer;

/**
 * Class QueryAnalyzerTest
 *
 * @package VitessPdoTest\PDO
 */
class QueryAnalyzerTest extends \PHPUnit_Framework_TestCase
{

    /**"SELECT * FROM user"
     * @var Analyzer
     */
    private $queryAnalyzer;

    /**
     *
     */
    protected function setUp()
    {
        $this->queryAnalyzer = new Analyzer();
    }

    /**
     *
     */
    public function testInsertQuery()
    {
        self::assertTrue($this->queryAnalyzer->isInsertQuery("INSERT INTO user (name) VALUES ('test_user')"));
        self::assertFalse($this->queryAnalyzer->isInsertQuery("SELECT * FROM user"));
    }

    /**
     *
     */
    public function testUpdateQuery()
    {
        self::assertTrue(
            $this->queryAnalyzer->isUpdateQuery(
                "update user set name = 'test_user_2' where name = 'test_user'"
            )
        );
        self::assertFalse($this->queryAnalyzer->isUpdateQuery("SELECT * FROM user"));
    }

    /**
     *
     */
    public function testDeleteQuery()
    {
        self::assertTrue($this->queryAnalyzer->isDeleteQuery("delete from user where name = 'test_user'"));
        self::assertFalse($this->queryAnalyzer->isDeleteQuery("SELECT * FROM user"));
    }
}
