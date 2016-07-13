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
