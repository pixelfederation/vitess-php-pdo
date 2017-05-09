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
 * Description of class EnginesMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class EnginesMember extends Member
{

    /**
     * @var array
     */
    private static $data = [
        [
            'Engine' => 'InnoDB',
            0 => 'InnoDB',
            'Support' => 'DEFAULT',
            1 => 'DEFAULT',
            'Comment' => 'Supports transactions, row-level locking, and foreign keys',
            2 => 'Supports transactions, row-level locking, and foreign keys',
            'Transactions' => 'YES',
            3 => 'YES',
            'XA' => 'YES',
            4 => 'YES',
            'Savepoints' => 'YES',
            5 => 'YES',
        ],
    ];

    /**
     * @var array
     */
    private static $fields = [
        'Engine',
        'Support',
        'Comment',
        'Transactions',
        'XA',
        'Savepoints',
    ];

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query instanceof ShowQuery || $query->getObject() !== ShowQuery::EXPRESSION_ENGINES) {
            return null;
        }

        return $this->getResultFromData(self::$data, self::$fields);
    }
}
