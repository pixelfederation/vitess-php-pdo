<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\SelectChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\Member;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\Query\Expression;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\SelectQuery;

/**
 * Description of class UseMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class ConnectionIdMember extends Member
{

    /**
     * @var array
     */
    private static $data = [
        [
            'CONNECTION_ID()' => '1',
            0 => '1',
        ],
    ];

    /**
     * @var array
     */
    private static $fields = [
        'CONNECTION_ID()',
    ];

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        /* @var $query SelectQuery */
        if (!$query->isType(QueryInterface::TYPE_SELECT)) {
            return null;
        }

        $field = $query->getFirstField();

        if ($field->getType() !== Expression::TYPE_FUNCTION
            || $field->getExpression() !== Expression::EXPR_CONNECTION_ID) {
            return null;
        }

        return $this->getResultFromData(self::$data, self::$fields);
    }
}
