<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
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
