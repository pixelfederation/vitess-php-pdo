<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\QueryAnalyzer\Query;

use VitessPdo\PDO\Exception;

/**
 * Description of class ColumnTypeExpression
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer\Query
 */
class ColumnTypeExpression extends ExpressionDecorator
{

    /**
     * @var Expression
     */
    private $dataDype;

    /**
     * @var string
     */
    private $sqlType;

    /**
     * @var bool
     */
    private $hasDefaultValue;

    /**
     * @var string
     */
    private $defaultValue;

    /**
     * @return DataTypeExpression
     * @throws Exception
     */
    public function getDataType()
    {
        if ($this->dataDype === null) {
            $dataType = $this->findFirstInSubTree(self::TYPE_DATA_TYPE);

            if (!$dataType) {
                throw new Exception('Unable to find data type.');
            }

            $this->dataDype = new DataTypeExpression($dataType);
        }

        return $this->dataDype;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSqlType()
    {
        if ($this->sqlType === null) {
            $dataType = $this->getDataType();
            $bracketExpr = $this->findFirstInSubTree(self::TYPE_BRACKET_EXPRESSION);
            $this->sqlType = $dataType->getExpression() . ($bracketExpr ? $bracketExpr->getExpression() : '');
        }

        return $this->sqlType;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isNullable()
    {
        return ((int) $this->getData(self::KEY_NULLABLE)) === 1;
    }

    /**
     * @return bool
     */
    public function hasDefault()
    {
        if ($this->hasDefaultValue === null) {
            $this->hasDefaultValue = $this->hasData(self::KEY_DEFAULT);

            if (!$this->hasDefaultValue) {
                $reserveds = $this->findAllInSubTreeAfterInclusive(self::TYPE_RESERVED);

                foreach ($reserveds as $reserved) {
                    if ($reserved->getExpression() === self::EXPR_DEFAULT) {
                        $this->hasDefaultValue = true;
                        break;
                    }
                }
            }
        }

        return $this->hasDefaultValue;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getDefault()
    {
        if ($this->defaultValue === null) {
            if (!$this->hasDefault()) {
                throw new Exception('There is no default value here.');
            }

            if ($this->hasData(self::KEY_DEFAULT)) {
                $this->defaultValue = trim($this->getData(self::KEY_DEFAULT), "'");

                return $this->defaultValue;
            }

            $reserveds = $this->findAllInSubTreeAfterInclusive(self::TYPE_RESERVED);

            foreach ($reserveds as $index => $reserved) {
                if ($reserved->getExpression() === self::EXPR_DEFAULT) {
                    $this->defaultValue = $reserveds[$index + 1]->getExpression();
                    break;
                }
            }
        }

        return $this->defaultValue;
    }
}
