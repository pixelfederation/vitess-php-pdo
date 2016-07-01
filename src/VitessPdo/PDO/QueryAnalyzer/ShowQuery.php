<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\QueryAnalyzer;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\QueryAnalyzer\Query\Expression;

/**
 * Description of class ShowQuery
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer
 */
class ShowQuery extends QueryDecorator
{

    /**
     * @const string
     */
    const TYPE = QueryInterface::TYPE_SHOW;

    /**
     * @const string
     */
    const EXPRESSION_TABLES = 'TABLES';

    /**
     * @const string
     */
    const EXPRESSION_DATABASES = 'DATABASES';

    /**
     * @const string
     */
    const EXPRESSION_TABLE_STATUS = 'TABLE STATUS';

    /**
     * @const string
     */
    const EXPRESSION_COLLATION = 'COLLATION';

    /**
     * @const string
     */
    const EXPRESSION_CREATE_DATABASE = 'CREATE DATABASE';

    /**
     * @const string
     */
    const EXPRESSION_CREATE_TABLE = 'CREATE TABLE';

    /**
     * @const string
     */
    const EXPRESSION_INDEX = 'INDEX';

    /**
     * @const string
     */
    const EXPRESSION_FULL_COLUMNS = 'FULL COLUMNS';

    /**
     * @var string
     */
    private $object;

    /**
     * @var int
     */
    private $afterObjectIndex;

    /**
     * @var string[]
     */
    private $expressionValues = [];

    /**
     * @var string
     */
    private $databaseExpression;

    /**
     *
     * @return string
     * @throws Exception
     */
    public function getObject()
    {
        if ($this->object === null) {
            /* @var Expression[] */
            $expressions = $this->getExpressions();

            if (!isset($expressions[0]) || $expressions[0] === Expression::EXPR_LIKE) {
                throw new Exception("Object missing.");
            }

            $stopExprs = [Expression::EXPR_LIKE, Expression::EXPR_FROM];
            $objectParts = [];
            /* @var $expr Expression */
            foreach ($expressions as $index => $expr) {
                if (in_array($expr->getExpression(), $stopExprs) || $expr->getNoQuotes()) {
                    $this->afterObjectIndex = $index;
                    break;
                }

                $objectParts[] = $expr->getExpression();
            }

            $this->object = implode(' ', $objectParts);
        }

        return $this->object;
    }

    /**
     * @return null|string
     */
    public function getLikeExpression()
    {
        return $this->getExressionValue(Expression::EXPR_LIKE);
    }

    /**
     * @return null|string
     */
    public function getFromExpression()
    {
        return $this->getExressionValue(Expression::EXPR_FROM);
    }

    /**
     * @param string $type
     * @return string
     * @throws Exception
     */
    public function getCreateObjectExpression($type)
    {
        if ($this->databaseExpression === null) {
            if ($this->getObject() !== $type) {
                throw new Exception("Not a SHOW {$type} query.");
            }

            $expressions = $this->getExpressions();

            if ($this->afterObjectIndex === null) {
                throw new Exception("Invalid SHOW {$type} query - object name missing.");
            }

            /* @var $databaseExpression Expression */
            $databaseExpression = $expressions[$this->afterObjectIndex];
            $this->databaseExpression = $databaseExpression->getNoQuotes()->getParts()[0];
        }

        return $this->databaseExpression;
    }

    /**
     * @param string $expressionType
     *
     * @return null|string
     */
    private function getExressionValue($expressionType)
    {
        if (!array_key_exists($expressionType, $this->expressionValues)) {
            $expressions = $this->getExpressions();
            $likeIndex = null;

            /* @var $expr Expression */
            foreach ($expressions as $index => $expr) {
                if ($expr->getExpression() === $expressionType) {
                    $likeIndex = $index;
                    break;
                }
            }

            if (!$likeIndex) {
                return null;
            }

            /* @var $field Expression */
            $field = $expressions[$likeIndex + 1];
            $this->expressionValues[$expressionType] = $field->getExpression();
            $noQuotes = $field->getNoQuotes();

            if ($noQuotes) {
                $this->expressionValues[$expressionType] = $noQuotes->getPartsAsString();
            }
        }

        return $this->expressionValues[$expressionType];
    }
}
