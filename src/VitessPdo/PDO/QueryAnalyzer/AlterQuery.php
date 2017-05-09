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
use VitessPdo\PDO\QueryAnalyzer\Query\CreateExpression;
use VitessPdo\PDO\QueryAnalyzer\Query\Expression;

/**
 * Description of class AlterQuery
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer
 */
class AlterQuery extends QueryDecorator
{

    /**
     * @var string
     */
    private $object;

    /**
     * @const string
     */
    const TYPE = QueryInterface::TYPE_ALTER;

    /**
     * @const string
     */
    const EXPRESSION_TABLE = 'TABLE';

    /**
     *
     * @return string
     * @throws Exception
     */
    public function getObject()
    {
        if ($this->object === null) {
            $parsed = $this->getParsedSql();

            if (!isset($parsed[1])) {
                throw new Exception("Object missing.");
            }

            $this->object = $parsed[1];
        }

        return $this->object;
    }

    /**
     * @return CreateExpression
     * @throws Exception
     */
    public function getObjectExpression()
    {
        $objectName = $this->getObject();
        $parsedSql = $this->getParsedSqlByExprType($objectName);

        return new Expression($parsedSql);
    }
}
