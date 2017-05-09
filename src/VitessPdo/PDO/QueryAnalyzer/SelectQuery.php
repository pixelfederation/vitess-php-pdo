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
use VitessPdo\PDO\QueryAnalyzer\Query\Expression;

/**
 * Description of class SelectQuery
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer
 */
class SelectQuery extends QueryDecorator
{

    /**
     * @var string
     */
    private $firstField;

    /**
     * @var string
     */
    private $fields;

    /**
     * @const string
     */
    const TYPE = QueryInterface::TYPE_SELECT;

    /**
     *
     * @return Expression
     * @throws Exception
     */
    public function getFirstField()
    {
        if ($this->firstField === null) {
            $fields = $this->getFields();

            if (!isset($fields[0])) {
                throw new Exception('First field missing.');
            }

            $this->firstField = $fields[0];
        }

        return $this->firstField;
    }

    /**
     * @return array|null|string
     */
    public function getFields()
    {
        if ($this->fields === null) {
            $this->fields = $this->getExpressions();
        }

        return $this->fields;
    }
}
