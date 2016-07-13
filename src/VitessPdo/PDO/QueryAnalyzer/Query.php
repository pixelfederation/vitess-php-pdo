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

use VitessPdo\PDO\Exception;

/**
 * Description of class Query
 *
 * @author  mfris
 * @package VitessPdo\PDO\Analyzer
 */
class Query implements QueryInterface
{

    /**
     * @var string
     */
    private $sql;

    /**
     * @var array
     */
    protected $parsedSql;

    const SQL_COMMAND_INSERT = 'INSERT';
    const SQL_COMMAND_UPDATE = 'UPDATE';
    const SQL_COMMAND_DELETE = 'DELETE';

    const EXPRESSION_TABLES = 'TABLES';
    const EXPRESSION_TABLE = 'TABLE';
    const EXPRESSION_STATUS = 'STATUS';
    const EXPRESSION_LIKE = 'LIKE';
    const EXPRESSION_COLLATION = 'COLLATION';
    const EXPRESSION_CREATE = 'CREATE';
    const EXPRESSION_DATABASE = 'DATABASE';
    const EXPRESSION_INDEX = 'INDEX';
    const EXPRESSION_FROM = 'FROM';

    /**
     * @var array
     */
    private static $types = [
        self::TYPE_SELECT => self::TYPE_SELECT,
        self::TYPE_INSERT => self::TYPE_INSERT,
        self::TYPE_UPDATE => self::TYPE_UPDATE,
        self::TYPE_DELETE => self::TYPE_DELETE,
        self::TYPE_USE    => self::TYPE_USE,
        self::TYPE_SHOW   => self::TYPE_SHOW,
        self::TYPE_CREATE => self::TYPE_CREATE,
        self::TYPE_ALTER => self::TYPE_ALTER,
        self::TYPE_DROP => self::TYPE_DROP,
    ];

    /**
     * Query constructor.
     *
     * @param string $sql
     * @param array $parsedSql
     */
    public function __construct($sql, array $parsedSql)
    {
        $this->sql = $sql;
        $this->parsedSql = $parsedSql;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @return array
     */
    public function getParsedSql()
    {
        return $this->parsedSql;
    }

    /**
     * @return bool
     */
    public function isInsert()
    {
        return isset($this->parsedSql[self::SQL_COMMAND_INSERT]);
    }

    /**
     * @return bool
     */
    public function isUpdate()
    {
        return isset($this->parsedSql[self::SQL_COMMAND_UPDATE]);
    }

    /**
     * @return bool
     */
    public function isDelete()
    {
        return isset($this->parsedSql[self::SQL_COMMAND_DELETE]);
    }

    /**
     * @return bool
     */
    public function isWritable()
    {
        return $this->isInsert()
            || $this->isUpdate()
            || $this->isDelete();
    }

    /**
     * @param string $type
     *
     * @return bool
     * @throws Exception
     */
    public function isType($type)
    {
        if (!$this->isTypeSupported($type)) {
            throw new Exception('Unsupported query type');
        }

        return isset($this->parsedSql[$type]);
    }

    /**
     * @param $type
     *
     * @return bool
     */
    private function isTypeSupported($type)
    {
        return isset(self::$types[$type]);
    }
}
