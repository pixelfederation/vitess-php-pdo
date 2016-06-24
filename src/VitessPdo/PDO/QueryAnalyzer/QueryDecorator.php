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
     */
    public function getParsedSql()
    {
        return $this->getDecoratedQuery()->getParsedSql()[static::TYPE];
    }

    /**
     * @return array
     */
    public function getExpressions()
    {
        if ($this->expressions === null) {
            $this->expressions = array_map(function ($expr) {
                return is_array($expr) ? new Expression($expr) : $expr;
            }, $this->getDecoratedQuery()->getParsedSql()[static::TYPE]);
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
