<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
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
     * @var bool
     */
    private $nullable;

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
     * @return bool
     * @throws Exception
     */
    public function isNullable()
    {
        if ($this->nullable === null) {
            $columnType = $this->findFirstInSubTree(self::TYPE_COLUMN_TYPE);

            if (!$columnType) {
                throw new Exception('Unable to identify column type.');
            }

            $reserved = $columnType->findFirstInSubTree(self::TYPE_RESERVED);

            if (!$reserved) {
                throw new Exception('Unable to identify nullable property.');
            }

            $this->nullable = in_array($reserved->getExpression(), [self::EXPR_NULL, self::EXPR_DEFAULT]);
        }

        return $this->nullable;
    }
}
