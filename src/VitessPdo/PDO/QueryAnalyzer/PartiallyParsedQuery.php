<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the PIXEL FEDERATION, s.r.o. nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL PIXEL FEDERATION, s.r.o. BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
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
