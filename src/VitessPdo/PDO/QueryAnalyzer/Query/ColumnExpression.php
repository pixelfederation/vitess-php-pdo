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
