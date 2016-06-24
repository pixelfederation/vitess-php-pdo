<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\QueryAnalyzer;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\QueryAnalyzer\Query\Expression;

/**
 * Description of class SelectQuery
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer
 */
class SelectQuery extends QueryDecorator
{

    /**
     * @var string
     */
    private $firstField;

    /**
     * @var string
     */
    private $fields;

    /**
     * @const string
     */
    const TYPE = QueryInterface::TYPE_SELECT;

    /**
     *
     * @return Expression
     * @throws Exception
     */
    public function getFirstField()
    {
        if ($this->firstField === null) {
            $fields = $this->getFields();

            if (!isset($fields[0])) {
                throw new Exception('First field missing.');
            }

            $this->firstField = $fields[0];
        }

        return $this->firstField;
    }

    /**
     * @return array|null|string
     */
    public function getFields()
    {
        if ($this->fields === null) {
            $this->fields = $this->getExpressions();
        }

        return $this->fields;
    }
}
