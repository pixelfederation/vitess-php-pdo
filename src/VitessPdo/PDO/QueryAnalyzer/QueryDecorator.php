<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\QueryAnalyzer;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\QueryAnalyzer\Query\Expression;

/**
 * Description of class QueryDecorator
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer
 */
abstract class QueryDecorator implements QueryInterface
{

    /**
     * @var QueryInterface
     */
    private $decoratedQuery;

    /**
     * @var Expression[]
     */
    private $expressions;

    /**
     * @const string
     */
    const TYPE = QueryInterface::TYPE_UNKNOWN;

    /**
     * QueryDecorator constructor.
     *
     * @param QueryInterface $decoratedQuery
     * @throws Exception
     */
    public function __construct(QueryInterface $decoratedQuery)
    {
        if (!$decoratedQuery->isType(static::TYPE)) {
            throw new Exception('Not a ' . static::TYPE . ' query.');
        }

        $this->decoratedQuery = $decoratedQuery;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->getDecoratedQuery()->getSql();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getParsedSql()
    {
        $type = static::TYPE;
        $parsedSql = $this->getDecoratedQuery()->getParsedSql();

        if (!isset($parsedSql[$type])) {
            throw new Exception("{$type} not found in expression.");
        }

        return $parsedSql[$type];
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getParsedSqlByExprType($type)
    {
        $parsedSql = $this->getDecoratedQuery()->getParsedSql();

        if (!isset($parsedSql[$type])) {
            throw new Exception("{$type} not found in expression.");
        }

        return $parsedSql[$type];
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function getExpressions()
    {
        if ($this->expressions === null) {
            $parsedSql = $this->getParsedSql();

            if (array_key_exists(0, $parsedSql)) {
                $this->expressions = array_map(function (array $expr) {
                    return new Expression($expr);
                }, $parsedSql);
            } else {
                $this->expressions = [new Expression($parsedSql)];
            }
        }

        return $this->expressions;
    }

    /**
     * @return bool
     */
    public function isInsert()
    {
        return $this->getDecoratedQuery()->isInsert();
    }

    /**
     * @return bool
     */
    public function isUpdate()
    {
        return $this->getDecoratedQuery()->isUpdate();
    }

    /**
     * @return bool
     */
    public function isDelete()
    {
        return $this->getDecoratedQuery()->isDelete();
    }

    /**
     * @return bool
     */
    public function isWritable()
    {
        return $this->getDecoratedQuery()->isWritable();
    }

    /**
     * @param string $type
     *
     * @return bool
     * @throws Exception
     */
    public function isType($type)
    {
        return $this->getDecoratedQuery()->isType($type);
    }

    /**
     * @return QueryInterface
     */
    protected function getDecoratedQuery()
    {
        return $this->decoratedQuery;
    }
}
