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

use PHPSQLParser\Options;
use PHPSQLParser\PHPSQLParser;

/**
 * Description of class Analyzer
 *
 * @author  mfris
 * @package VitessPdo\PDO
 */
class Analyzer
{

    /**
     * @var PHPSQLParser
     */
    private $parser;

    /**
     * Analyzer constructor.
     */
    public function __construct()
    {
        $this->parser = new PHPSQLParser(false, false, [Options::CONSISTENT_SUB_TREES => true]);
    }

    /**
     * @param string $sql
     * @return boolean
     */
    public function isInsertQuery($sql)
    {
        return $this->parseQuery($sql)->isInsert();
    }

    /**
     * @param string $sql
     * @return boolean
     */
    public function isUpdateQuery($sql)
    {
        return $this->parseQuery($sql)->isUpdate();
    }

    /**
     * @param string $sql
     * @return boolean
     */
    public function isDeleteQuery($sql)
    {
        return $this->parseQuery($sql)->isDelete();
    }

    /**
     * @param string $sql
     *
     * @return bool
     */
    public function isWritableQuery($sql)
    {
        $result = $this->parseQuery($sql);

        return $result->isWritable();
    }

    /**
     * @param string $sql
     *
     * @return QueryInterface
     */
    public function parseQuery($sql)
    {
        return new PartiallyParsedQuery($sql, $this->parser);
    }
}
