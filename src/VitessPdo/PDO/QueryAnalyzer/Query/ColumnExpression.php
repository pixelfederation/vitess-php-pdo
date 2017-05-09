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

namespace VitessPdo\PDO\QueryAnalyzer\Query;

use VitessPdo\PDO\Exception;

/**
 * Description of class ColumnExpression
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer\Query
 */
class ColumnExpression extends ExpressionDecorator
{

    /**
     * @var string
     */
    private $columnName;

    /**
     * @var ColumnTypeExpression
     */
    private $columnType;

    /**
     * @return string
     * @throws Exception
     */
    public function getColumnName()
    {
        if ($this->columnName === null) {
            $columnRef = $this->findFirstInSubTree(self::TYPE_COLUMN_REF);

            if (!$columnRef) {
                throw new Exception('It appears that the column name is missing in the column definition.');
            }

            $this->columnName = $columnRef->getNoQuotes()->getPartsAsString();
        }

        return $this->columnName;
    }

    /**
     * @return ColumnTypeExpression
     * @throws Exception
     */
    public function getColumnType()
    {
        if ($this->columnType === null) {
            $columnType = $this->findFirstInSubTree(self::TYPE_COLUMN_TYPE);

            if (!$columnType) {
                throw new Exception('Unable to identify column type.');
            }

            $this->columnType = new ColumnTypeExpression($columnType);
        }

        return $this->columnType;
    }
}
