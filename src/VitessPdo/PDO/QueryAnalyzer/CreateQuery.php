<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\QueryAnalyzer;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\QueryAnalyzer\Query\CreateExpression;
use VitessPdo\PDO\QueryAnalyzer\Query\Expression;

/**
 * Description of class CreateQuery
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer
 */
class CreateQuery extends QueryDecorator
{

    /**
     * @var string
     */
    private $object;

    /**
     * @const string
     */
    const TYPE = QueryInterface::TYPE_CREATE;

    /**
     * @const string
     */
    const EXPRESSION_TABLE = 'TABLE';

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

            if (!isset($expressions[0])) {
                throw new Exception("Object missing.");
            }

            $stopExprs = [];
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
     * @return CreateExpression
     * @throws Exception
     */
    public function getObjectExpression()
    {
        $objectName = $this->getObject();
        $parsedSql = $this->getParsedSqlByExprType($objectName);

        return new CreateExpression(new Expression($parsedSql));
    }
}
