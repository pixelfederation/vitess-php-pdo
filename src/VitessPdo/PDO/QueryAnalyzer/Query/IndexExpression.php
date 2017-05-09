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

use Doctrine\Common\Collections\ArrayCollection;
use VitessPdo\PDO\Exception;

/**
 * Description of class IndexExpression
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer\Query
 */
class IndexExpression extends ExpressionDecorator
{

    /**
     * @var ArrayCollection
     */
    private $columns;

    /**
     * @var string[]
     */
    private $columnNames;

    /**
     * @const string
     */
    const KEY_NAME_PRIMARY = 'PRIMARY';

    /**
     * IndexExpression constructor.
     *
     * @param ExpressionInterface $expression
     * @param ArrayCollection $columns
     */
    public function __construct(ExpressionInterface $expression, ArrayCollection $columns)
    {
        parent::__construct($expression);
        $this->columns = $this->filterColumns($columns);
    }

    /**
     * @return int
     * @todo make more precise
     */
    public function getNonUnique()
    {
        return $this->getType() === self::TYPE_PRIMARY_KEY ? 0 : 1;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getKeyName()
    {
        if ($this->getType() === self::TYPE_PRIMARY_KEY) {
            return self::KEY_NAME_PRIMARY;
        }

        return $this->getIndexName();
    }

    /**
     * @return ArrayCollection|ColumnExpression[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param ColumnExpression $column
     *
     * @return bool
     */
    public function hasColumn(ColumnExpression $column)
    {
        return $this->columns->contains($column);
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getIndexName()
    {
        if ($this->getType() !== self::TYPE_INDEX) {
            throw new Exception('Expression is not an index.');
        }

        $expression = $this->findFirstInSubTree(self::TYPE_CONST);

        if (!$expression) {
            throw new Exception('Index name probably missing.');
        }

        return trim($expression->getExpression(), '`');
    }

    /**
     * @param ArrayCollection $unfiltered
     *
     * @return ArrayCollection
     */
    private function filterColumns(ArrayCollection $unfiltered)
    {
        $columnNames = $this->getColumnNames();

        $filtered = $unfiltered->filter(function (ColumnExpression $column) use ($columnNames) {
            return in_array($column->getColumnName(), $columnNames);
        });

        return new ArrayCollection($filtered->getValues());
    }

    /**
     * @return array|\string[]
     */
    private function getColumnNames()
    {
        if ($this->columnNames === null) {
            $columnList = $this->findFirstInSubTree(self::TYPE_COLUMN_LIST);
            $indexColumns = $columnList->getSubTree();
            $this->columnNames = array_map(function (Expression $expression) {
                return $expression->getNoQuotes()->getPartsAsString();
            }, $indexColumns);
        }

        return $this->columnNames;
    }
}
