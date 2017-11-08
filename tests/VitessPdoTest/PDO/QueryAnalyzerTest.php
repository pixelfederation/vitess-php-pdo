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

use VitessPdo\PDO\QueryAnalyzer\Analyzer;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryAnalyzerTest
 *
 * @package VitessPdoTest\PDO
 */
class QueryAnalyzerTest extends TestCase
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
