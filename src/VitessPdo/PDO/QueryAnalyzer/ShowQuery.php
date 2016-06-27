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
     * @var string
     */
    private $object;

    /**
     * @var int
     */
    private $afterObjectIndex;

    /**
     * @var string
     */
    private $likeExpression;

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

            $objectParts = [];
            /* @var $expr Expression */
            foreach ($expressions as $index => $expr) {
                if ($expr->getExpression() === Expression::EXPR_LIKE || $expr->getNoQuotes()) {
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
        if ($this->likeExpression === null) {
            $expressions = $this->getExpressions();
            $likeIndex = null;

            /* @var $expr Expression */
            foreach ($expressions as $index => $expr) {
                if ($expr->getExpression() === Expression::EXPR_LIKE) {
                    $likeIndex = $index;
                    break;
                }
            }

            if (!$likeIndex) {
                return null;
            }

            /* @var $field Expression */
            $field = $expressions[$likeIndex + 1];
            $this->likeExpression = $field->getExpression();
        }

        return $this->likeExpression;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDatabaseExpression()
    {
        if ($this->databaseExpression === null) {
            if ($this->getObject() !== self::EXPRESSION_CREATE_DATABASE) {
                throw new Exception('Not a SHOW CREATE DATABASE query.');
            }

            $expressions = $this->getExpressions();

            if ($this->afterObjectIndex === null) {
                throw new Exception('Invalid SHOW CREATE DATABASE query - database name missing.');
            }

            /* @var $databaseExpression Expression */
            $databaseExpression = $expressions[$this->afterObjectIndex];
            $this->databaseExpression = $databaseExpression->getNoQuotes()->getParts()[0];
        }

        return $this->databaseExpression;
    }
}
