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
