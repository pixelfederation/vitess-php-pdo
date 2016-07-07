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
 * Description of class DropQuery
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer
 */
class DropQuery extends QueryDecorator
{

    /**
     * @var string
     */
    private $object;

    /**
     * @const string
     */
    const TYPE = QueryInterface::TYPE_DROP;

    /**
     * @const string
     */
    const EXPRESSION_TABLE = 'TABLE';

    /**
     * @const string
     */
    const EXPRESSION_EXPRESSION = 'expression';

    /**
     *
     * @return string
     * @throws Exception
     */
    public function getObject()
    {
        if ($this->object === null) {
            /* @var $expression Expression */
            $expression = $this->getExpressions()[0];
            /* @var $expressions Expression[] */
            $expressions = $expression->getSubTree();

            if (!isset($expressions[0])) {
                throw new Exception("Object missing.");
            }

            $stopExprTypes = [self::EXPRESSION_EXPRESSION];
            $objectParts = [];
            /* @var $expr Expression */
            foreach ($expressions as $index => $expr) {
                if (in_array($expr->getType(), $stopExprTypes) || $expr->getNoQuotes()) {
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
        $parsedSql = $this->getParsedSql($objectName);

        return new CreateExpression(new Expression($parsedSql));
    }
}
