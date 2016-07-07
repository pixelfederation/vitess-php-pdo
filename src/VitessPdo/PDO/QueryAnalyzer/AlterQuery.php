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
 * Description of class AlterQuery
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer
 */
class AlterQuery extends QueryDecorator
{

    /**
     * @var string
     */
    private $object;

    /**
     * @const string
     */
    const TYPE = QueryInterface::TYPE_ALTER;

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
            $parsed = $this->getParsedSql();

            if (!isset($parsed[1])) {
                throw new Exception("Object missing.");
            }

            $this->object = $parsed[1];
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

        return new Expression($parsedSql);
    }
}
