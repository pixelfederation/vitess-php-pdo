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

namespace VitessPdo\PDO\MySql\QueryHandler\SelectChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\Member;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\Query\Expression;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\SelectQuery;

/**
 * Description of class UseMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class ConnectionIdMember extends Member
{

    /**
     * @var array
     */
    private static $data = [
        [
            'CONNECTION_ID()' => '1',
            0 => '1',
        ],
    ];

    /**
     * @var array
     */
    private static $fields = [
        'CONNECTION_ID()',
    ];

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        /* @var $query SelectQuery */
        if (!$query->isType(QueryInterface::TYPE_SELECT)) {
            return null;
        }

        $field = $query->getFirstField();

        if ($field->getType() !== Expression::TYPE_FUNCTION
            || $field->getExpression() !== Expression::EXPR_CONNECTION_ID) {
            return null;
        }

        return $this->getResultFromData(self::$data, self::$fields);
    }
}
