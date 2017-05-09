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

/**
 * Description of class CreateExpression
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer\Query
 */
class CreateExpression extends ExpressionDecorator
{

    /**
     * @var ColumnExpression[]|ArrayCollection
     */
    private $columns;

    /**
     * @var IndexExpression[]
     */
    private $indices;

    /**
     * @return ArrayCollection|ColumnExpression[]
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function getColumns()
    {
        if ($this->columns === null) {
            $this->columns = new ArrayCollection();

            foreach ($this->getCreateDef()->getSubTree() as $expression) {
                if ($expression->getType() === self::TYPE_COLUMN_DEF) {
                    $this->columns->add(new ColumnExpression($expression));
                } else {
                    break;
                }
            }
        }

        return $this->columns;
    }

    /**
     * @return IndexExpression[]
     */
    public function getIndices()
    {
        if ($this->indices === null) {
            $this->indices = [];
            $allowedTypes = [self::TYPE_PRIMARY_KEY, self::TYPE_INDEX];
            $columns = $this->getColumns();

            foreach ($this->getCreateDef()->getSubTree() as $expression) {
                if (in_array($expression->getType(), $allowedTypes)) {
                    $this->indices[] = new IndexExpression($expression, $columns);
                }
            }
        }

        return $this->indices;
    }
}
