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

namespace VitessPdo\PDO\QueryAnalyzer;

use PHPSQLParser\PHPSQLParser;

/**
 * Description of class PartiallyParsedQuery
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer
 */
class PartiallyParsedQuery extends QueryDecorator
{

    /**
     * @var QueryInterface
     */
    private $query;

    /**
     * @var string
     */
    private $sql;

    /**
     * @var PHPSQLParser
     */
    private $parser;

    /**
     * @var string
     */
    private $firstSqlKeyword;

    /**
     * PartiallyParsedQuery constructor.
     *
     * @param string $sql
     * @param PHPSQLParser $parser
     */
    public function __construct($sql, PHPSQLParser $parser)
    {
        $this->sql = $sql;
        $this->parser = $parser;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @return bool
     */
    public function isInsert()
    {
        return $this->getFirstSqlKeyword() === self::TYPE_INSERT;
    }

    /**
     * @return bool
     */
    public function isUpdate()
    {
        return $this->getFirstSqlKeyword() === self::TYPE_UPDATE;
    }

    /**
     * @return bool
     */
    public function isDelete()
    {
        return $this->getFirstSqlKeyword() === self::TYPE_DELETE;
    }

    /**
     * @return bool
     */
    public function isWritable()
    {
        return in_array($this->getFirstSqlKeyword(), [self::TYPE_INSERT, self::TYPE_UPDATE, self::TYPE_DELETE]);
    }

    /**
     * @return array
     */
    public function getParsedSql()
    {
        return $this->getDecoratedQuery()->getParsedSql();
    }

    /**
     * @return QueryInterface
     */
    protected function getDecoratedQuery()
    {
        if ($this->query === null) {
            $this->query = new Query($this->sql, $this->parser->parse($this->sql));
        }

        return $this->query;
    }

    /**
     * @return string
     */
    private function getFirstSqlKeyword()
    {
        if ($this->firstSqlKeyword === null) {
            $spacePos = strpos($this->sql, ' ');
            $keyword = substr($this->sql, 0, $spacePos);
            $this->firstSqlKeyword = strtoupper($keyword);
        }

        return $this->firstSqlKeyword;
    }
}
