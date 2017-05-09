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

namespace VitessPdo\PDO\MySql\QueryHandler\ShowChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\Member;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\ShowQuery;

/**
 * Description of class CreateDatabaseMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class CreateDatabaseMember extends Member
{

    /**
     * @var array
     */
    private static $data = [
        'Database' => '{DB}',
        0 => '{DB}',
        'Create Database' => 'CREATE DATABASE `{DB}` /*!40100 DEFAULT CHARACTER SET utf8 */',
        1 => 'CREATE DATABASE `{DB}` /*!40100 DEFAULT CHARACTER SET utf8 */',
    ];

    /**
     * @var array
     */
    private static $fields = [
        'Database',
        'Create Database',
    ];

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query instanceof ShowQuery || $query->getObject() !== ShowQuery::EXPRESSION_CREATE_DATABASE) {
            return null;
        }

        $database = $query->getCreateObjectExpression(ShowQuery::EXPRESSION_CREATE_DATABASE);

        $newData = [];
        $newData[] = array_map(function ($row) use ($database) {
            return str_replace('{DB}', $database, $row);
        }, self::$data);

        return $this->getResultFromData($newData, self::$fields);
    }
}
